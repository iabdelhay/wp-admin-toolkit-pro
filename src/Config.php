<?php
namespace WPAdminToolkitPro;

use InvalidArgumentException;
use WPAdminToolkitPro\Contracts\SingletonContract;
use WPAdminToolkitPro\Core\Singleton;

class Config implements SingletonContract
{
    use Singleton;

    private ?array $adminFolder = null;
    private ?string $pluginRootDirectory = null;
    private ?string $pluginMainDirectory = null;

    public function __construct(
        private readonly string $pluginKey = 'wp_admin_toolkit_pro', 
        private readonly string $pluginName = 'WP Admin toolkit pro',
        private readonly string $version = '1.0.0',
        ?string $pluginRootDirectory = null,
        ?string $pluginMainDirectory = null,
    )
    {
        if ($pluginRootDirectory !== null) {
            $this->setPluginRootDirectory($pluginRootDirectory);
        }

        if ($pluginMainDirectory !== null) {
            $this->setPluginMainDirectory($pluginMainDirectory);
        }
    }

    public function getPluginKey(): string
    {
        return $this->pluginKey;
    }

    public function getPluginName(): string
    {
        return $this->pluginName;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Get the plugin root directory.
     *
     * @return string|null
     */
    public function getPluginRootDirectory(): ?string
    {
        return $this->pluginRootDirectory;
    }

    /**
     * Get the plugin main directory.
     *
     * @return string|null
     */
    public function getPluginMainDirectory(): ?string
    {
        return $this->pluginMainDirectory;
    }


    public function setAdminFolder(string|array $adminFolder = 'admin'): static
    {
        $folders = is_array($adminFolder) ? $adminFolder : [$adminFolder];

        foreach ($folders as $folder) {
            $this->assertFolderExists($folder);
        }

        $this->adminFolder = array_values($folders);

        return $this;
    }

    public function getAdminFolder(): array
    {
        if($this->adminFolder && count($this->adminFolder) > 0) {
            return $this->adminFolder;
        }

        if($this->resolveFolderPath('admin')){
            return ['admin'];
        }

          if($this->resolveFolderPath('Admin')){
            return ['Admin'];
        }
        
        return [];
    }

    /**
     * set the plugin root directory.
     *
     * @return string
     */
    public function setPluginRootDirectory(string $pluginRootDirectory): static
    {
        if (is_null($this->pluginRootDirectory)) {
            $this->pluginRootDirectory = $this->normalizeDirectoryPath($pluginRootDirectory, 'plugin root directory');
        }

        return $this;
    }

    /**
     * Set the plugin main directory.
     *
     * @return string
     */
    public function setPluginMainDirectory(string $pluginMainDirectory): static
    {
        if (is_null($this->pluginMainDirectory)) {
            $this->pluginMainDirectory = $this->normalizeDirectoryPath($pluginMainDirectory, 'plugin main directory');
        }

        return $this;
    }

    private function normalizeDirectoryPath(string $path, string $context): string
    {
        $path = trim($path);
        $candidates = [$path];

        if ($path !== '' && !$this->isAbsolutePath($path)) {
            if ($this->pluginRootDirectory !== null) {
                $candidates[] = $this->pluginRootDirectory . DIRECTORY_SEPARATOR . $path;
            }

            if ($this->pluginMainDirectory !== null) {
                $candidates[] = $this->pluginMainDirectory . DIRECTORY_SEPARATOR . $path;
            }
        }

        foreach ($candidates as $candidate) {
            $normalized = realpath($candidate);

            if ($normalized !== false && is_dir($normalized)) {
                return $normalized;
            }
        }

        throw new InvalidArgumentException(sprintf('The %s "%s" is not a valid directory.', $context, $path));
    }

    private function assertFolderExists(string $folder): void
    {
        $folder = trim($folder);

        if ($folder === '') {
            throw new InvalidArgumentException(printf('Folder path "%s" does not exist.', $folder));
        }

        $resolved = $this->resolveFolderPath($folder);

        if ($resolved === null) {
            throw new InvalidArgumentException(sprintf('Folder path "%s" does not exist.', $folder));
        }
    }

    private function resolveFolderPath(string $folder): ?string
    {
        if ($this->isAbsolutePath($folder)) {
            $normalized = realpath($folder);

            return ($normalized !== false && is_dir($normalized)) ? $normalized : null;
        }

        $candidates = [];

        if ($this->pluginMainDirectory !== null) {
            $candidates[] = $this->pluginMainDirectory . DIRECTORY_SEPARATOR . $folder;
        }

        if ($this->pluginRootDirectory !== null) {
            $candidates[] = $this->pluginRootDirectory . DIRECTORY_SEPARATOR . $folder;
        }

        foreach ($candidates as $candidate) {
            $normalized = realpath($candidate);

            if ($normalized !== false && is_dir($normalized)) {
                return $normalized;
            }
        }

        return null;
    }

    private function isAbsolutePath(string $path): bool
    {
        if ($path === '') {
            return false;
        }

        if ($path[0] === DIRECTORY_SEPARATOR) {
            return true;
        }

        if (strlen($path) > 1 && $path[1] === ':' && preg_match('/^[A-Za-z]:/', $path) === 1) {
            return true;
        }

        return strncmp($path, '\\', 2) === 0;
    }
}
