<?php
class Custom_Greeting_Updater {
    private $file;
    private $plugin;
    private $basename;
    private $active;
    private $username;
    private $repository;
    private $authorize_token;

    public function __construct($file) {
        $this->file = $file;
        add_action('admin_init', array($this, 'set_plugin_properties'));
        return $this;
    }

    public function set_plugin_properties() {
        $this->plugin = get_plugin_data($this->file);
        $this->basename = plugin_basename($this->file);
        $this->active = is_plugin_active($this->basename);
    }

    public function set_username($username) {
        $this->username = $username;
    }

    public function set_repository($repository) {
        $this->repository = $repository;
    }

    public function authorize($token) {
        $this->authorize_token = $token;
    }

    private function get_repository_info() {
        return (object) [
            'authorize_token' => $this->authorize_token,
            'slug' => $this->repository,
            'name' => $this->plugin['Name'],
            'plugin_uri' => $this->plugin['PluginURI'],
            'version' => $this->plugin['Version'],
            'author' => $this->plugin['AuthorName'],
            'download_link' => $this->get_download_link(),
            'trunk' => $this->plugin['PluginURI'],
            'requires' => '3.0',
            'tested' => '5.8',
            'last_updated' => $this->plugin['LastUpdated'],
            'sections' => array(
                'description' => $this->plugin['Description'],
                'changelog' => $this->get_latest_changelog(),
            ),
            'banners' => array(
                'low' => '',
                'high' => '',
            ),
        ];
    }

    private function get_download_link() {
        return sprintf('https://github.com/%s/%s/archive/master.zip', $this->username, $this->repository);
    }

    private function get_latest_changelog() {
        $url = sprintf('https://raw.githubusercontent.com/%s/%s/master/CHANGELOG.md', $this->username, $this->repository);
        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            return '';
        }
        $changelog = wp_remote_retrieve_body($response);
        return $changelog;
    }

    public function initialize() {
        add_filter('pre_set_site_transient_update_plugins', array($this, 'modify_transient'), 10, 1);
        add_filter('plugins_api', array($this, 'plugin_popup'), 10, 3);
        add_filter('upgrader_post_install', array($this, 'after_install'), 10, 3);
    }

    public function modify_transient($transient) {
        if (property_exists($transient, 'checked')) {
            if ($checked = $transient->checked) {
                $information = $this->get_repository_info();
                $out_of_date = version_compare($information->version, $checked[$this->basename], '>');
                if ($out_of_date) {
                    $new_files = $this->get_repository_info();
                    $plugin = array(
                        'url' => $information->plugin_uri,
                        'slug' => $information->slug,
                        'package' => $information->download_link,
                        'new_version' => $information->version,
                    );
                    $transient->response[$this->basename] = (object) $plugin;
                }
            }
        }
        return $transient;
    }

    public function plugin_popup($result, $action, $args) {
        if (!empty($args->slug) && $args->slug == $this->basename) {
            $information = $this->get_repository_info();
            return (object) array(
                'name' => $information->name,
                'slug' => $this->basename,
                'version' => $information->version,
                'author' => $information->author,
                'requires' => $information->requires,
                'tested' => $information->tested,
                'downloaded' => 0,
                'last_updated' => $information->last_updated,
                'sections' => $information->sections,
                'download_link' => $information->download_link,
            );
        }
        return $result;
    }

    public function after_install($response, $hook_extra, $result) {
        global $wp_filesystem;
        $install_directory = plugin_dir_path($this->file);
        $wp_filesystem->move($result['destination'], $install_directory);
        $result['destination'] = $install_directory;
        if ($this->active) {
            activate_plugin($this->basename);
        }
        return $result;
    }
}


