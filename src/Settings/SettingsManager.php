<?php
namespace WPAdminToolkitPro\Settings;

use WPAdminToolkitPro\Config;
use WPAdminToolkitPro\Contracts\SingletonContract;
use WPAdminToolkitPro\Core\Singleton;

class SettingsManager implements SingletonContract
{
    use Singleton;

    public array $fields = [];
    private Config $config;

    public function __construct()
    {
        $this->config = Config::instance();
    }

    public static function init(...$args): static
    {
        $instace = self::instance(...$args);

        $instace->registerHooks();

        return $instace;
    }

    public function registerHooks(): void
    {
        add_action('admin_init', [$this, 'registerSettings']);
        add_action('admin_init', [$this, 'registerFields']);
    }

    public function registerSettings() {
        
        register_setting($this->config->getPluginKey(), $this->config->getPluginKey());

        add_settings_section(
            $this->config->getPluginKey(),
            'Main Settings',
            [$this, 'renderPage'],
            $this->config->getPluginKey()
        );
    }

    public function registerFields(): void
    {
        foreach($this->fields as $field){
            add_settings_field(
                $field['id'],
                $field['title'],
                [$this, 'renderField'],
                $this->config->getPluginKey(),
                $field['section'],
                [
                    'label_for' => $field['id'], 
                    'field_type' => $field['field_type'], 
                    'options' => $field['options'], 
                    'description' => $field['description']
                ]
            );
        }
    }

    public function addSettingField($id, $title, $field_type = 'checkbox', $section = null, $description = '', $options = []) {

        if(empty($section)){
            $section = $this->config->getPluginKey();
        }

        $this->fields[] = compact('id','title', 'field_type', 'section', 'description', 'options');
    }   

    public function renderPage($args) {

        $pluginKey = $this->config->getPluginKey();
        $pluginName = $this->config->getPluginName();

        ?>
        <p id="<?php echo esc_attr($args['id']); ?>"><?php esc_html_e("the main settings of the ".$pluginName." plguin", $pluginKey); ?></p>
        <?php
    }

    public function renderField($args) {

        $options = get_option($this->config->getPluginKey());

        $value = isset($options[$args['label_for']]) ? $options[$args['label_for']] : '';
        $name = $this->getGenerateFieldName($args['label_for']);
        $description = $args['description'] ?? '';

        switch ($args['field_type']) {
            case 'checkbox':
                echo '<input type="checkbox" id="' . esc_attr($args['label_for']) . '" name="' . esc_attr($name) . '" value="1" ' . checked($value, 1, false) . '>';
                break;
            case 'textarea':
                echo '<textarea cols="50" row="150" id="' . esc_attr($args['label_for']) . '" name="' . esc_attr($name) . '">' . esc_textarea($value) . '</textarea>';
                break;
            case 'editor':
                $settings = array(
                    'textarea_rows' => 15,
                    'textarea_name' => $name
                );

                wp_editor($value, $args['label_for'], $settings);
                break;
            case 'action_button':
                $config = is_array($description) ? $description : [];
                $this->renderActionButton($args['label_for'], $config);
                return;
            case 'select':
                if (is_array($options) && !empty($options)) {
                    echo '<select id="' . esc_attr($args['label_for']) . '" name="' . esc_attr($name) . '">';
                    foreach ($options as $optionValue => $optionLabel) {
                        echo '<option value="' . esc_attr($optionValue) . '" ' . selected($value, $optionValue, false) . '>' . esc_html($optionLabel) . '</option>';
                    }
                    echo '</select>';
                } else {
                    echo '<p>' . esc_html__('No options available.', 'mobzella') . '</p>';
                }
                break;
            case 'text':
            default:
                echo '<input type="text" class="regular-text" id="' . esc_attr($args['label_for']) . '" name="' . esc_attr($name) . ']" value="' . esc_attr($value) . '">';
                break;
            // Additional case handlers for other types of inputs can be added here.
        }

        if (!empty($description)) {
            if (is_array($description) && $this->isListArray($description)) {
                echo '<ul>' . implode('', array_map(function ($item) {
                    return '<li>' . esc_html($item) . '</li>';
                }, $description)) . '</ul>';
            } else {
                echo '<p>' . esc_html(is_array($description) ? implode(' ', $description) : $description) . '</p>';
            }
        }
    }

    private function renderActionButton(string $fieldId, array $config): void
    {
        $action = $config['action'] ?? $fieldId;
        $buttonText = $config['button_text'] ?? __('Run Action', 'mobzella');
        $workingText = $config['working_text'] ?? __('Processing…', 'mobzella');
        $help = $config['help'] ?? '';
        $statusOption = $config['status_option'] ?? '';
        $nonce = wp_create_nonce($action);

        $summaryHtml = '';
        if ($statusOption) {
            $status = get_option($statusOption);
            $summaryHtml = $this->formatStatusSummary(is_array($status) ? $status : null);
        }

        echo '<div class="mobzella-action-button__wrap" data-field="' . esc_attr($fieldId) . '">';
        echo '<button type="button" class="button button-primary mobzella-action-button" data-action="' . esc_attr($action) . '" data-nonce="' . esc_attr($nonce) . '" data-working-text="' . esc_attr($workingText) . '" data-default-text="' . esc_attr($buttonText) . '">' . esc_html($buttonText) . '</button>';
        echo '<span class="spinner mobzella-action-button__spinner" style="float:none"></span>';
        echo '<div class="mobzella-action-button__status">' . $summaryHtml . '</div>';
        if (!empty($help)) {
            echo '<p class="description">' . esc_html($help) . '</p>';
        }
        echo '</div>';

        static $scriptPrinted = false;
        if (!$scriptPrinted) {
            $scriptPrinted = true;
            ?>
            <script>
            (function($){
                $(document).on('click', '.mobzella-action-button', function(e){
                    e.preventDefault();
                    const $btn = $(this);
                    if ($btn.prop('disabled')) {
                        return;
                    }

                    const action = $btn.data('action');
                    const nonce = $btn.data('nonce');
                    const defaultText = $btn.data('default-text');
                    const workingText = $btn.data('working-text');
                    const $wrap = $btn.closest('.mobzella-action-button__wrap');
                    const $spinner = $wrap.find('.mobzella-action-button__spinner');
                    const $status = $wrap.find('.mobzella-action-button__status');

                    $btn.prop('disabled', true).text(workingText);
                    $spinner.addClass('is-active');
                    $status.html('');

                    $.post(ajaxurl, {
                        action: action,
                        _ajax_nonce: nonce
                    }).done(function(response){
                        if (response && response.success) {
                            var data = response.data || {};
                            if (data.summary_html) {
                                $status.html(data.summary_html);
                            }
                            if (data.message && !data.summary_html) {
                                $status.html('<div class="notice notice-success"><p>' + data.message + '</p></div>');
                            }
                        } else {
                            var errorMessage = (response && response.data && response.data.message) ? response.data.message : '<?php echo esc_js(__('Unexpected error occurred while processing the request.', 'mobzella')); ?>';
                            $status.html('<div class="notice notice-error"><p>' + errorMessage + '</p></div>');
                        }
                    }).fail(function(jqXHR){
                        var errorMessage = jqXHR.responseJSON && jqXHR.responseJSON.data && jqXHR.responseJSON.data.message ? jqXHR.responseJSON.data.message : jqXHR.statusText;
                        if (!errorMessage) {
                            errorMessage = '<?php echo esc_js(__('Something went wrong. Please try again.', 'mobzella')); ?>';
                        }
                        $status.html('<div class="notice notice-error"><p>' + errorMessage + '</p></div>');
                    }).always(function(){
                        $btn.prop('disabled', false).text(defaultText);
                        $spinner.removeClass('is-active');
                    });
                });
            })(jQuery);
            </script>
            <?php
        }
    }

    private function formatStatusSummary(?array $status): string
    {
        if (empty($status)) {
            return '<p class="description">' . esc_html__('Seeder has not been executed yet.', 'mobzella') . '</p>';
        }

        $statusClass = 'notice notice-success';
        $state = $status['status'] ?? 'success';
        if ($state === 'error') {
            $statusClass = 'notice notice-error';
        } elseif ($state === 'locked') {
            $statusClass = 'notice notice-warning';
        }

        $createdCount = (int)($status['created_count'] ?? count($status['created'] ?? []));
        $deletedCount = (int)($status['deleted_count'] ?? count($status['deleted'] ?? []));
        $duration = isset($status['duration']) ? (string)$status['duration'] : '';
        $ranAt = $status['ran_at'] ?? '';
        $message = $status['message'] ?? '';

        $parts = [];
        if ($ranAt) {
            $parts[] = sprintf(esc_html__('Last run: %s', 'mobzella'), esc_html($ranAt));
        }
        $parts[] = sprintf(esc_html__('Created %d group(s)', 'mobzella'), $createdCount);
        $parts[] = sprintf(esc_html__('Deleted %d group(s)', 'mobzella'), $deletedCount);
        if ($duration !== '') {
            $parts[] = sprintf(esc_html__('Duration: %s seconds', 'mobzella'), esc_html($duration));
        }
        if (!empty($status['missing_attributes'])) {
            $parts[] = sprintf(
                esc_html__('Missing attributes: %s', 'mobzella'),
                esc_html(implode(', ', $status['missing_attributes']))
            );
        }
        if ($message && $state !== 'success') {
            $parts[] = esc_html($message);
        }

        return '<div class="' . esc_attr($statusClass) . '"><p>' . implode(' | ', $parts) . '</p></div>';
    }

    private function isListArray(array $value): bool
    {
        if (function_exists('array_is_list')) {
            return array_is_list($value);
        }

        $expected = 0;
        foreach ($value as $key => $_) {
            if ($key !== $expected) {
                return false;
            }
            $expected++;
        }
        return true;
    }

    private function getGenerateFieldName(string $fieldId): string
    {
        return $this->config->getPluginKey() . '[' . $fieldId . ']';
    }

    public static function getSettings(): array
    {
        $settings = get_option(self::instance()->config->getPluginKey());

        return  !empty($settings) ? $settings : [];
    }
}
