<?php
/**
 * Plugin Name: Usf Résumé
 * Plugin URI:  https://github.com/Godde3s
 * Description: A practical résumé/CV plugin — manage experience, education, skills and projects from one settings page, render anywhere with the [usf_resume] shortcode. Includes schema.org Person JSON-LD for SEO, responsive styling and zero bloat.
 * Version:     1.0.0
 * Author:      Usf (Godde3s)
 * Author URI:  https://github.com/Godde3s
 * License:     MIT
 * Text Domain: usf-resume
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit; // No direct access.
}

final class Usf_Resume {

        const OPTION_KEY = 'usf_resume_data';
        const CAP        = 'manage_options';

        /** @var Usf_Resume|null */
        private static $instance = null;

        /** @var bool */
        private $css_printed = false;

        /** Drop stored data when the plugin is uninstalled. */
        public static function uninstall() {
                delete_option( self::OPTION_KEY );
        }

        public static function instance() {
                if ( null === self::$instance ) {
                        self::$instance = new self();
                }
                return self::$instance;
        }

        private function __construct() {
                add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
                add_action( 'admin_init', array( $this, 'register_settings' ) );
                add_shortcode( 'usf_resume', array( $this, 'render_shortcode' ) );
                add_filter(
                        'plugin_action_links_' . plugin_basename( __FILE__ ),
                        array( $this, 'action_links' )
                );
        }

        /* -----------------------------------------------------------------
         * Settings page
         * --------------------------------------------------------------- */

        public function add_settings_page() {
                add_menu_page(
                        __( 'Résumé', 'usf-resume' ),
                        __( 'Résumé', 'usf-resume' ),
                        self::CAP,
                        'usf-resume',
                        array( $this, 'render_settings_page' ),
                        'dashicons-id-alt',
                        65
                );
        }

        public function register_settings() {
                register_setting( 'usf_resume', self::OPTION_KEY, array( $this, 'sanitize' ) );
        }

        public function action_links( $links ) {
                $url = admin_url( 'admin.php?page=usf-resume' );
                array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Edit résumé', 'usf-resume' ) . '</a>' );
                return $links;
        }

        private function defaults() {
                return array(
                        'name'       => '',
                        'headline'   => '',
                        'email'      => '',
                        'location'   => '',
                        'summary'    => '',
                        'experience' => '',
                        'education'  => '',
                        'projects'   => '',
                        'skills'     => '',
                );
        }

        private function row_sections() {
                return array(
                        'experience' => __( 'Experience', 'usf-resume' ),
                        'education'  => __( 'Education', 'usf-resume' ),
                        'projects'   => __( 'Projects', 'usf-resume' ),
                );
        }

        /**
         * Line format everywhere:  Period | Title | Org | Details
         * Skills format:           Skill name | Level
         */
        public function sanitize( $in ) {
                $out = $this->defaults();
                if ( ! is_array( $in ) ) {
                        return $out;
                }

                foreach ( array( 'name', 'headline', 'location' ) as $k ) {
                        $out[ $k ] = sanitize_text_field( isset( $in[ $k ] ) ? $in[ $k ] : '' );
                }
                $out['email']   = sanitize_email( isset( $in['email'] ) ? $in['email'] : '' );
                $out['summary'] = sanitize_textarea_field( isset( $in['summary'] ) ? $in['summary'] : '' );

                foreach ( array_merge( array_keys( $this->row_sections() ), array( 'skills' ) ) as $k ) {
                        $out[ $k ] = sanitize_textarea_field( isset( $in[ $k ] ) ? $in[ $k ] : '' );
                }
                return $out;
        }

        public function render_settings_page() {
                if ( ! current_user_can( self::CAP ) ) {
                        wp_die( esc_html__( 'You do not have permission to access this page.', 'usf-resume' ) );
                }
                $d = wp_parse_args( get_option( self::OPTION_KEY, array() ), $this->defaults() );
                ?>
                <div class="wrap">
                        <h1><?php esc_html_e( 'Usf Résumé', 'usf-resume' ); ?></h1>
                        <p>
                                <?php esc_html_e( 'Fill in your résumé once, then place the shortcode', 'usf-resume' ); ?>
                                <code>[usf_resume]</code>
                                <?php esc_html_e( 'on any page or post. A single section can be shown with', 'usf-resume' ); ?>
                                <code>[usf_resume section="skills"]</code>.
                        </p>

                        <form method="post" action="options.php">
                                <?php settings_fields( 'usf_resume' ); ?>

                                <h2 class="title"><?php esc_html_e( 'Personal info', 'usf-resume' ); ?></h2>
                                <table class="form-table" role="presentation">
                                        <tr>
                                                <th><label for="usf-name"><?php esc_html_e( 'Full name', 'usf-resume' ); ?></label></th>
                                                <td><input id="usf-name" name="usf_resume_data[name]" type="text" class="regular-text" value="<?php echo esc_attr( $d['name'] ); ?>"></td>
                                        </tr>
                                        <tr>
                                                <th><label for="usf-headline"><?php esc_html_e( 'Headline', 'usf-resume' ); ?></label></th>
                                                <td><input id="usf-headline" name="usf_resume_data[headline]" type="text" class="regular-text" placeholder="<?php esc_attr_e( 'Software engineer && system designer', 'usf-resume' ); ?>" value="<?php echo esc_attr( $d['headline'] ); ?>"></td>
                                        </tr>
                                        <tr>
                                                <th><label for="usf-email"><?php esc_html_e( 'Email', 'usf-resume' ); ?></label></th>
                                                <td><input id="usf-email" name="usf_resume_data[email]" type="email" class="regular-text" value="<?php echo esc_attr( $d['email'] ); ?>"></td>
                                        </tr>
                                        <tr>
                                                <th><label for="usf-location"><?php esc_html_e( 'Location', 'usf-resume' ); ?></label></th>
                                                <td><input id="usf-location" name="usf_resume_data[location]" type="text" class="regular-text" value="<?php echo esc_attr( $d['location'] ); ?>"></td>
                                        </tr>
                                        <tr>
                                                <th><label for="usf-summary"><?php esc_html_e( 'Summary', 'usf-resume' ); ?></label></th>
                                                <td><textarea id="usf-summary" name="usf_resume_data[summary]" rows="4" class="large-text"><?php echo esc_textarea( $d['summary'] ); ?></textarea></td>
                                        </tr>
                                </table>

                                <h2 class="title"><?php esc_html_e( 'Sections', 'usf-resume' ); ?></h2>
                                <p class="description">
                                        <?php esc_html_e( 'One entry per line. Format:', 'usf-resume' ); ?>
                                        <code><?php esc_html_e( 'Period | Title | Organization | Details', 'usf-resume' ); ?></code>
                                </p>
                                <?php foreach ( $this->row_sections() as $key => $label ) : ?>
                                        <table class="form-table" role="presentation">
                                                <tr>
                                                        <th><label for="usf-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
                                                        <td>
                                                                <textarea id="usf-<?php echo esc_attr( $key ); ?>" name="usf_resume_data[<?php echo esc_attr( $key ); ?>]" rows="6" class="large-text code" placeholder="2024 - now | Backend engineer | Acme | Built the public REST API"><?php echo esc_textarea( $d[ $key ] ); ?></textarea>
                                                        </td>
                                                </tr>
                                        </table>
                                <?php endforeach; ?>

                                <p class="description">
                                        <?php esc_html_e( 'One skill per line. Format:', 'usf-resume' ); ?>
                                        <code><?php esc_html_e( 'Skill | Level', 'usf-resume' ); ?></code>
                                </p>
                                <table class="form-table" role="presentation">
                                        <tr>
                                                <th><label for="usf-skills"><?php esc_html_e( 'Skills', 'usf-resume' ); ?></label></th>
                                                <td>
                                                        <textarea id="usf-skills" name="usf_resume_data[skills]" rows="8" class="large-text code" placeholder="Python | Advanced"><?php echo esc_textarea( $d['skills'] ); ?></textarea>
                                                </td>
                                        </tr>
                                </table>

                                <?php submit_button(); ?>
                        </form>
                </div>
                <?php
        }

        /* -----------------------------------------------------------------
         * Parsing helpers
         * --------------------------------------------------------------- */

        private function parse_rows( $raw ) {
                $rows = array();
                foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw ) as $line ) {
                        $line = trim( $line );
                        if ( '' === $line ) {
                                continue;
                        }
                        $parts  = array_map( 'trim', explode( '|', $line ) );
                        $rows[] = array(
                                'period'  => isset( $parts[0] ) ? $parts[0] : '',
                                'title'   => isset( $parts[1] ) ? $parts[1] : '',
                                'org'     => isset( $parts[2] ) ? $parts[2] : '',
                                'details' => isset( $parts[3] ) ? $parts[3] : '',
                        );
                }
                return $rows;
        }

        private function parse_skills( $raw ) {
                $out = array();
                foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw ) as $line ) {
                        $line = trim( $line );
                        if ( '' === $line ) {
                                continue;
                        }
                        $parts = array_map( 'trim', explode( '|', $line ) );
                        $out[] = array(
                                'name'  => isset( $parts[0] ) ? $parts[0] : '',
                                'level' => isset( $parts[1] ) ? $parts[1] : '',
                        );
                }
                return $out;
        }

        private function allowed_details_html() {
                return array(
                        'a'      => array( 'href' => array(), 'title' => array(), 'target' => array(), 'rel' => array() ),
                        'strong' => array(),
                        'em'     => array(),
                        'br'     => array(),
                );
        }

        /* -----------------------------------------------------------------
         * Shortcode rendering
         * --------------------------------------------------------------- */

        public function render_shortcode( $atts ) {
                $a = shortcode_atts(
                        array( 'section' => '' ),
                        $atts,
                        'usf_resume'
                );

                $d = wp_parse_args( get_option( self::OPTION_KEY, array() ), $this->defaults() );
                $this->print_css();

                $sections = array( 'experience', 'education', 'projects', 'skills' );
                $wanted   = '' !== $a['section'] ? array( sanitize_key( $a['section'] ) ) : $sections;

                $labels = array_merge( $this->row_sections(), array( 'skills' => __( 'Skills', 'usf-resume' ) ) );

                ob_start();
                ?>
                <div class="usf-resume">
                        <?php if ( '' === $a['section'] ) : ?>
                                <header class="usf-resume__head">
                                        <h2 class="usf-resume__name"><?php echo esc_html( $d['name'] ); ?></h2>
                                        <?php if ( $d['headline'] ) : ?>
                                                <p class="usf-resume__headline"><?php echo esc_html( $d['headline'] ); ?></p>
                                        <?php endif; ?>
                                        <p class="usf-resume__meta">
                                                <?php if ( $d['email'] ) : ?>
                                                        <a href="mailto:<?php echo esc_attr( $d['email'] ); ?>"><?php echo esc_html( $d['email'] ); ?></a>
                                                <?php endif; ?>
                                                <?php if ( $d['location'] ) : ?>
                                                        <span><?php echo esc_html( $d['location'] ); ?></span>
                                                <?php endif; ?>
                                        </p>
                                        <?php if ( $d['summary'] ) : ?>
                                                <p class="usf-resume__summary"><?php echo esc_html( $d['summary'] ); ?></p>
                                        <?php endif; ?>
                                </header>
                        <?php endif; ?>

                        <?php foreach ( $wanted as $key ) : ?>
                                <?php if ( ! isset( $labels[ $key ] ) ) { continue; } ?>

                                <?php if ( 'skills' === $key ) : ?>
                                        <?php $skills = $this->parse_skills( $d['skills'] ); ?>
                                        <?php if ( $skills ) : ?>
                                                <section class="usf-resume__section">
                                                        <h3 class="usf-resume__title"><?php echo esc_html( $labels[ $key ] ); ?></h3>
                                                        <ul class="usf-resume__skills">
                                                                <?php foreach ( $skills as $s ) : ?>
                                                                        <li>
                                                                                <span class="usf-resume__skill-name"><?php echo esc_html( $s['name'] ); ?></span>
                                                                                <?php if ( $s['level'] ) : ?>
                                                                                        <span class="usf-resume__skill-level"><?php echo esc_html( $s['level'] ); ?></span>
                                                                                <?php endif; ?>
                                                                        </li>
                                                                <?php endforeach; ?>
                                                        </ul>
                                                </section>
                                        <?php endif; ?>
                                <?php else : ?>
                                        <?php $rows = $this->parse_rows( $d[ $key ] ); ?>
                                        <?php if ( $rows ) : ?>
                                                <section class="usf-resume__section">
                                                        <h3 class="usf-resume__title"><?php echo esc_html( $labels[ $key ] ); ?></h3>
                                                        <?php foreach ( $rows as $row ) : ?>
                                                                <article class="usf-resume__item">
                                                                        <p class="usf-resume__item-head">
                                                                                <?php if ( $row['period'] ) : ?>
                                                                                        <span class="usf-resume__period"><?php echo esc_html( $row['period'] ); ?></span>
                                                                                <?php endif; ?>
                                                                                <span class="usf-resume__item-title"><?php echo esc_html( $row['title'] ); ?></span>
                                                                                <?php if ( $row['org'] ) : ?>
                                                                                        <span class="usf-resume__org"><?php echo esc_html( $row['org'] ); ?></span>
                                                                                <?php endif; ?>
                                                                        </p>
                                                                        <?php if ( $row['details'] ) : ?>
                                                                                <p class="usf-resume__item-details"><?php echo wp_kses( $row['details'], $this->allowed_details_html() ); ?></p>
                                                                        <?php endif; ?>
                                                                </article>
                                                        <?php endforeach; ?>
                                                </section>
                                        <?php endif; ?>
                                <?php endif; ?>
                        <?php endforeach; ?>
                </div>
                <?php
                if ( '' === $a['section'] ) {
                        $this->print_jsonld( $d );
                }
                return (string) ob_get_clean();
        }

        private function print_jsonld( $d ) {
                if ( '' === $d['name'] ) {
                        return;
                }
                $knows = array();
                foreach ( $this->parse_skills( $d['skills'] ) as $s ) {
                        if ( '' !== $s['name'] ) {
                                $knows[] = $s['name'];
                        }
                }
                $graph = array(
                        '@context'     => 'https://schema.org',
                        '@type'        => 'Person',
                        'name'         => $d['name'],
                        'email'        => '' !== $d['email'] ? 'mailto:' . $d['email'] : null,
                        'jobTitle'     => '' !== $d['headline'] ? $d['headline'] : null,
                        'homeLocation' => '' !== $d['location'] ? array( '@type' => 'Place', 'name' => $d['location'] ) : null,
                        'description'  => '' !== $d['summary'] ? $d['summary'] : null,
                        'knowsAbout'   => $knows ? $knows : null,
                );
                echo '<script type="application/ld+json">' . wp_json_encode( array_filter( $graph ) ) . '</script>';
        }

        private function print_css() {
                if ( $this->css_printed ) {
                        return;
                }
                $this->css_printed = true;
                ?>
                <style id="usf-resume-css">
                        .usf-resume{max-width:820px;margin:0 auto;font-size:16px;line-height:1.6;color:#1c1b17}
                        .usf-resume__head{border-bottom:2px solid #1c1b17;padding-bottom:16px;margin-bottom:24px}
                        .usf-resume__name{margin:0 0 4px;font-size:clamp(26px,4vw,38px);line-height:1.1}
                        .usf-resume__headline{margin:0 0 8px;font-size:18px;color:#6e6a5e}
                        .usf-resume__meta{margin:0;display:flex;gap:16px;flex-wrap:wrap;font-size:14px;color:#6e6a5e}
                        .usf-resume__meta a{color:inherit}
                        .usf-resume__summary{margin:14px 0 0}
                        .usf-resume__section{margin:0 0 30px}
                        .usf-resume__title{font-size:13px;letter-spacing:.14em;text-transform:uppercase;color:#6e6a5e;margin:0 0 14px;padding-bottom:8px;border-bottom:1px solid rgba(28,27,23,.15)}
                        .usf-resume__item{padding:14px 0;border-bottom:1px solid rgba(28,27,23,.09)}
                        .usf-resume__item:last-child{border-bottom:0}
                        .usf-resume__item-head{margin:0;display:flex;gap:12px;flex-wrap:wrap;align-items:baseline}
                        .usf-resume__period{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:12.5px;color:#be3d26}
                        .usf-resume__item-title{font-weight:600}
                        .usf-resume__org{color:#6e6a5e}
                        .usf-resume__item-details{margin:6px 0 0;font-size:15px;color:#43413a}
                        .usf-resume__skills{list-style:none;margin:0;padding:0;display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:8px 20px}
                        .usf-resume__skills li{display:flex;justify-content:space-between;gap:10px;border-bottom:1px dashed rgba(28,27,23,.15);padding:6px 0}
                        .usf-resume__skill-name{font-weight:500}
                        .usf-resume__skill-level{font-size:12.5px;color:#6e6a5e}
                        @media print{.usf-resume__section{break-inside:avoid}}
                </style>
                <?php
        }
}

Usf_Resume::instance();

/* Clean up on uninstall. */
register_uninstall_hook( __FILE__, array( 'Usf_Resume', 'uninstall' ) );