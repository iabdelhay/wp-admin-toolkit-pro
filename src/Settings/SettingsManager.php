<?php
namespace WPAdminToolkitPro\Settings;

use WPAdminToolkitPro\Config;
use WPAdminToolkitPro\Contracts\SingletonContract;
use WPAdminToolkitPro\core\Singleton;

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
                ['label_for' => $field['id'], 'field_type' => 'checkbox']
            );
        }
    }

    public function addSettingField($id, $title, $section = null) {

        if(empty($section)){
            $section = $this->config->getPluginKey();
        }

        $this->fields[] = compact('id','title','section');
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
       
        switch ($args['field_type']) {
            case 'checkbox':
                echo '<input type="checkbox" id="'. $args['label_for'] .'" name="'.$this->config->getPluginKey().'['. $args['label_for'] .']" value="1" '. checked($value, 1, false) .'>';
                break;
            // Additional case handlers for other types of inputs can be added here.
        }
    }

    public static function getSettings(): array
    {
        $settings = get_option(self::instance()->config->getPluginKey());

        return  !empty($settings) ? $settings : [];
    }
}
