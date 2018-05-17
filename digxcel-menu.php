<?php
if ( ! class_exists( 'DigxcelMenu' ) ) {

  class DigxcelMenu{

    public function __construct() {
      add_action( 'admin_init', function() {
        register_setting( 'digxcel-settings', 'digxcel_key' );
        register_setting( 'digxcel-settings', 'digxcel_cookie_widget_key' );
        register_setting( 'digxcel-settings', 'digxcel_cookie_widget_enabled' );
      });
    }

    public function digxcel_create_menu() {
        add_action('admin_menu', array($this, 'digxcel_setup_menu'));
    }

    function digxcel_setup_menu() {
        add_menu_page('DigXcel Configuration', 'DigXcel', 'manage_options', 'digxcel', array($this,'digxcel_settings'));
    }

    function digxcel_settings() {
      ?>
        <div class="wrap">
          <form action="options.php" method="post">
            <?php
              settings_fields( 'digxcel-settings' );
              do_settings_sections( 'digxcel-settings' );
            ?>
            <table style="text-align: left;">
                <tr>
                    <th>Digxcel API Key</th>
                    <td><input type="password" name="digxcel_key" value="<?php echo esc_attr( get_option('digxcel_key') ); ?>" size="50" /></td>
                </tr>
                <tr>
                    <th>Cookie consent widget Key</th>
                    <td><input type="string" name="digxcel_cookie_widget_key" value="<?php echo esc_attr( get_option('digxcel_cookie_widget_key') ); ?>" size="50" /></td>
                </tr>
                <tr>
                    <th>Cookie consent widget enabled</th>
                    <td><input name="digxcel_cookie_widget_enabled" type="checkbox" id="digxcel_cookie_widget_enabled" value="true" <?php checked('true', get_option('digxcel_cookie_widget_enabled', 'true')); ?> /></td>
                </tr>
                <tr>
                    <td><?php submit_button(); ?></td>
                </tr>
            </table>
          </form>
        </div>
      <?php
    }
  }
}
