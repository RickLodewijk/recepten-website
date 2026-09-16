<?php
/**
 * Shortcode Handler for [acf_spin_wheel]
 *
 * @package ACF_Spin_Wheel
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Spin_Wheel_Shortcode {

    /**
     * Shortcode tag
     */
    public const TAG = 'acf_spin_wheel';

    /**
     * Singleton instance
     *
     * @var ACF_Spin_Wheel_Shortcode|null
     */
    private static ?ACF_Spin_Wheel_Shortcode $instance = null;

    /**
     * Get instance
     *
     * @return ACF_Spin_Wheel_Shortcode
     */
    public static function get_instance(): ACF_Spin_Wheel_Shortcode {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_shortcode( self::TAG, [ $this, 'render_shortcode' ] );
    }

    /**
     * Render the shortcode output
     *
     * @param array|string $atts
     * @return string
     */
    public function render_shortcode( $atts = [] ): string {
        ACF_Spin_Wheel_Assets::get_instance()->enqueue_assets();

        $atts = shortcode_atts( [
            'title' => '',
        ], is_array( $atts ) ? $atts : [] );

        $is_logged_in  = is_user_logged_in();
        $is_shared     = ! empty( $_GET['wheel'] );
        $require_login = (bool) get_option( 'acf_spin_wheel_require_login', 0 );
        $default_title = ! empty( $atts['title'] ) ? sanitize_text_field( $atts['title'] ) : __( 'My Spin Wheel', 'acf-spin-wheel' );
        $current_url   = is_singular() ? get_permalink() : ( ! empty( $_SERVER['REQUEST_URI'] ) ? home_url( add_query_arg( [] ) ) : home_url( '/' ) );
        $login_url     = wp_login_url( $current_url );

        // Default sample entries for brand new wheel
        $default_entries = implode( "\n", [
            __( 'Pizza', 'acf-spin-wheel' ),
            __( 'Burgers', 'acf-spin-wheel' ),
            __( 'Sushi', 'acf-spin-wheel' ),
            __( 'Pasta', 'acf-spin-wheel' ),
            __( 'Tacos', 'acf-spin-wheel' ),
            __( 'Salad', 'acf-spin-wheel' ),
            __( 'Curry', 'acf-spin-wheel' ),
            __( 'BBQ', 'acf-spin-wheel' ),
        ] );

        ob_start();
        ?>
        <div class="acf-wheel-root" id="acf-wheel-app" data-is-logged-in="<?php echo $is_logged_in ? '1' : '0'; ?>" data-is-shared="<?php echo $is_shared ? '1' : '0'; ?>" data-require-login="<?php echo $require_login ? '1' : '0'; ?>">

            <!-- Shared Wheel Banner (Shown in read-only mode) -->
            <div class="acf-wheel-banner" id="acf-wheel-shared-banner" style="<?php echo $is_shared ? '' : 'display: none;'; ?>">
                <div class="acf-wheel-banner-icon">🎡</div>
                <div class="acf-wheel-banner-content">
                    <strong id="acf-wheel-banner-title"><?php esc_html_e( 'Viewing shared wheel', 'acf-spin-wheel' ); ?></strong>
                    <p id="acf-wheel-banner-desc">
                        <?php if ( ! $is_logged_in && $require_login ) : ?>
                            <?php esc_html_e( 'You are viewing a shared wheel in read-only mode. Log in to customize and save your own wheels.', 'acf-spin-wheel' ); ?>
                        <?php else : ?>
                            <?php esc_html_e( 'You are viewing a shared wheel. You can spin it, or click "Create My Own" to make your own version.', 'acf-spin-wheel' ); ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="acf-wheel-banner-actions">
                    <?php if ( ! $is_logged_in && $require_login ) : ?>
                        <a href="<?php echo esc_url( $login_url ); ?>" class="acf-wheel-btn acf-wheel-btn-secondary acf-wheel-btn-sm">
                            <?php esc_html_e( 'Log In', 'acf-spin-wheel' ); ?>
                        </a>
                    <?php else : ?>
                        <button type="button" class="acf-wheel-btn acf-wheel-btn-primary acf-wheel-btn-sm" id="acf-wheel-btn-clone">
                            <?php esc_html_e( 'Create My Own', 'acf-spin-wheel' ); ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Upper Section: Canvas Wheel & Live Editor -->
            <div class="acf-wheel-upper-section">

                <!-- Left Column: Canvas Wheel -->
                <div class="acf-wheel-stage-col">
                    <div class="acf-wheel-canvas-container" id="acf-wheel-stage">
                        <!-- Ticker pointer marker -->
                        <div class="acf-wheel-pointer" id="acf-wheel-pointer" title="<?php esc_attr_e( 'Pointer', 'acf-spin-wheel' ); ?>">
                            <svg viewBox="0 0 40 50" class="acf-wheel-pointer-svg">
                                <path d="M20,48 L4,10 C2,6 5,2 10,2 L30,2 C35,2 38,6 36,10 Z" fill="#E11D48" stroke="#FFFFFF" stroke-width="3" stroke-linejoin="round" />
                                <circle cx="20" cy="14" r="5" fill="#FFFFFF" />
                            </svg>
                        </div>

                        <!-- Canvas Wheel -->
                        <canvas id="acf-wheel-canvas" width="600" height="600" aria-label="<?php esc_attr_e( 'Interactive Spin Wheel', 'acf-spin-wheel' ); ?>" role="img"></canvas>

                        <!-- Center Spin Button Hub -->
                        <button type="button" class="acf-wheel-center-hub" id="acf-wheel-spin-btn" aria-label="<?php esc_attr_e( 'Spin the Wheel', 'acf-spin-wheel' ); ?>">
                            <span class="acf-wheel-center-text"><?php esc_html_e( 'SPIN', 'acf-spin-wheel' ); ?></span>
                        </button>
                    </div>

                    <!-- Stage quick info / sound toggle -->
                    <div class="acf-wheel-stage-meta">
                        <div class="acf-wheel-stage-meta-left">
                            <span class="acf-wheel-slices-badge" id="acf-wheel-slices-count">8 slices</span>
                            <button type="button" class="acf-wheel-btn-reset-badge" id="acf-wheel-stage-reset" style="display: none;" title="<?php esc_attr_e( 'Reset rad en herstel alle opties', 'acf-spin-wheel' ); ?>">
                                🔄 <?php esc_html_e( 'Reset rad', 'acf-spin-wheel' ); ?> (<span class="acf-wheel-removed-stage-count">0</span>)
                            </button>
                        </div>
                        <button type="button" class="acf-wheel-sound-toggle" id="acf-wheel-sound-toggle" title="<?php esc_attr_e( 'Toggle Sound Effects', 'acf-spin-wheel' ); ?>">
                            <span class="acf-wheel-sound-icon" id="acf-wheel-sound-icon">🔊</span>
                            <span class="acf-wheel-sound-label"><?php esc_html_e( 'Sound On', 'acf-spin-wheel' ); ?></span>
                        </button>
                    </div>
                </div>

                <!-- Right Column: Input & Controls -->
                <div class="acf-wheel-editor-col" id="acf-wheel-editor">
                    <!-- Wheel Title -->
                    <div class="acf-wheel-field-group">
                        <label for="acf-wheel-title-input" class="acf-wheel-label">
                            <?php esc_html_e( 'Wheel Title', 'acf-spin-wheel' ); ?>
                        </label>
                        <input type="text" id="acf-wheel-title-input" class="acf-wheel-input" placeholder="<?php esc_attr_e( 'e.g. Lunch Decider', 'acf-spin-wheel' ); ?>" value="<?php echo esc_attr( $default_title ); ?>" />
                    </div>

                    <!-- Entries Textarea -->
                    <div class="acf-wheel-field-group acf-wheel-field-entries">
                        <div class="acf-wheel-label-row">
                            <label for="acf-wheel-entries-input" class="acf-wheel-label">
                                <?php esc_html_e( 'Entries', 'acf-spin-wheel' ); ?>
                                <span class="acf-wheel-label-hint"><?php esc_html_e( '(One per line)', 'acf-spin-wheel' ); ?></span>
                            </label>
                            <div class="acf-wheel-quick-tools">
                                <button type="button" class="acf-wheel-btn-tool acf-wheel-btn-tool-reset" id="acf-wheel-btn-reset-wheel" style="display: none;" title="<?php esc_attr_e( 'Zet alle verwijderde opties weer terug', 'acf-spin-wheel' ); ?>">
                                    🔄 <?php esc_html_e( 'Reset rad', 'acf-spin-wheel' ); ?> (<span id="acf-wheel-removed-count">0</span>)
                                </button>
                                <button type="button" class="acf-wheel-btn-tool" id="acf-wheel-btn-shuffle" title="<?php esc_attr_e( 'Randomize entries order', 'acf-spin-wheel' ); ?>">
                                    🔀 <?php esc_html_e( 'Shuffle', 'acf-spin-wheel' ); ?>
                                </button>
                                <button type="button" class="acf-wheel-btn-tool" id="acf-wheel-btn-sort" title="<?php esc_attr_e( 'Sort alphabetically', 'acf-spin-wheel' ); ?>">
                                    🔤 <?php esc_html_e( 'Sort', 'acf-spin-wheel' ); ?>
                                </button>
                            </div>
                        </div>
                        <textarea id="acf-wheel-entries-input" class="acf-wheel-textarea" rows="9" spellcheck="false" placeholder="<?php esc_attr_e( 'Enter one option per line...', 'acf-spin-wheel' ); ?>"><?php echo esc_textarea( $default_entries ); ?></textarea>

                        <!-- Eliminated / Spun entries container -->
                        <div class="acf-wheel-eliminated-box" id="acf-wheel-eliminated-box" style="display: none;">
                            <div class="acf-wheel-eliminated-header">
                                <span class="acf-wheel-eliminated-title">🎯 <?php esc_html_e( 'Reeds gedraaid', 'acf-spin-wheel' ); ?> (<span id="acf-wheel-eliminated-box-count">0</span>):</span>
                                <button type="button" class="acf-wheel-btn-link" id="acf-wheel-btn-reset-all" title="<?php esc_attr_e( 'Zet alle gedraaide opties terug in het rad', 'acf-spin-wheel' ); ?>">
                                    🔄 <?php esc_html_e( 'Alles herstellen', 'acf-spin-wheel' ); ?>
                                </button>
                            </div>
                            <div class="acf-wheel-eliminated-chips" id="acf-wheel-eliminated-chips"></div>
                        </div>
                    </div>

                    <!-- Primary Actions -->
                    <div class="acf-wheel-actions">
                        <button type="button" class="acf-wheel-btn acf-wheel-btn-primary" id="acf-wheel-btn-save">
                            <span class="acf-wheel-btn-icon">💾</span>
                            <span class="acf-wheel-btn-text" id="acf-wheel-save-btn-text"><?php esc_html_e( 'Save Wheel', 'acf-spin-wheel' ); ?></span>
                            <span class="acf-wheel-spinner" style="display:none;"></span>
                        </button>
                        <button type="button" class="acf-wheel-btn acf-wheel-btn-outline" id="acf-wheel-btn-new">
                            <span class="acf-wheel-btn-icon">✨</span>
                            <span class="acf-wheel-btn-text"><?php esc_html_e( 'New Wheel', 'acf-spin-wheel' ); ?></span>
                        </button>
                        <button type="button" class="acf-wheel-btn acf-wheel-btn-outline" id="acf-wheel-btn-share">
                            <span class="acf-wheel-btn-icon">🔗</span>
                            <span class="acf-wheel-btn-text"><?php esc_html_e( 'Copy Share Link', 'acf-spin-wheel' ); ?></span>
                        </button>
                    </div>

                    <!-- Save Status / Meta indicator -->
                    <div class="acf-wheel-status-bar" id="acf-wheel-status-bar" style="display: none;">
                        <span class="acf-wheel-status-dot"></span>
                        <span class="acf-wheel-status-text" id="acf-wheel-status-text"></span>
                    </div>
                </div>

            </div>

            <!-- Lower Section: Saved Wheels Grid -->
            <div class="acf-wheel-lower-section" id="acf-wheel-saved-section">
                <div class="acf-wheel-saved-header">
                    <div class="acf-wheel-saved-title-wrap">
                        <h3 class="acf-wheel-saved-title">
                            🎯 <?php esc_html_e( 'Saved Wheels', 'acf-spin-wheel' ); ?>
                        </h3>
                        <span class="acf-wheel-count-badge" id="acf-wheel-saved-count">0</span>
                    </div>
                </div>

                <!-- Saved Wheels Grid -->
                <div class="acf-wheel-saved-grid" id="acf-wheel-saved-grid">
                    <div class="acf-wheel-loading-placeholder" id="acf-wheel-saved-loader">
                        <div class="acf-wheel-spinner"></div>
                        <span><?php esc_html_e( 'Loading saved wheels...', 'acf-spin-wheel' ); ?></span>
                    </div>
                </div>
            </div>

            <!-- MODAL: Winner Announcement -->
            <div class="acf-wheel-modal-backdrop" id="acf-wheel-winner-modal" style="display: none;" role="dialog" aria-modal="true">
                <div class="acf-wheel-modal-dialog acf-wheel-modal-winner">
                    <!-- Confetti Canvas inside dialog -->
                    <canvas id="acf-wheel-confetti-canvas" class="acf-wheel-confetti-layer"></canvas>

                    <button type="button" class="acf-wheel-modal-close" id="acf-wheel-winner-close" aria-label="<?php esc_attr_e( 'Close', 'acf-spin-wheel' ); ?>">&times;</button>
                    <div class="acf-wheel-winner-badge">🎉 <?php esc_html_e( 'WINNER', 'acf-spin-wheel' ); ?> 🎉</div>
                    <h2 class="acf-wheel-winner-name" id="acf-wheel-winner-text">Option Name</h2>
                    <p class="acf-wheel-winner-subtext"><?php esc_html_e( 'The wheel has spoken!', 'acf-spin-wheel' ); ?></p>
                    <div class="acf-wheel-modal-actions">
                        <button type="button" class="acf-wheel-btn acf-wheel-btn-primary" id="acf-wheel-btn-spin-again">
                            🔄 <?php esc_html_e( 'Spin Again', 'acf-spin-wheel' ); ?>
                        </button>
                        <button type="button" class="acf-wheel-btn acf-wheel-btn-danger" id="acf-wheel-btn-remove-winner" title="<?php esc_attr_e( 'Haal deze winnaar uit het rad voor de volgende ronde', 'acf-spin-wheel' ); ?>">
                            🗑️ <?php esc_html_e( 'Haal eruit', 'acf-spin-wheel' ); ?>
                        </button>
                        <button type="button" class="acf-wheel-btn acf-wheel-btn-outline" id="acf-wheel-btn-winner-dismiss">
                            <?php esc_html_e( 'Close', 'acf-spin-wheel' ); ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- MODAL: Guest Save Prompt -->
            <div class="acf-wheel-modal-backdrop" id="acf-wheel-login-modal" style="display: none;" role="dialog" aria-modal="true">
                <div class="acf-wheel-modal-dialog">
                    <button type="button" class="acf-wheel-modal-close" id="acf-wheel-login-close" aria-label="<?php esc_attr_e( 'Close', 'acf-spin-wheel' ); ?>">&times;</button>
                    <div class="acf-wheel-modal-icon">🔐</div>
                    <h3 class="acf-wheel-modal-title"><?php esc_html_e( 'Log In to Save Your Wheel', 'acf-spin-wheel' ); ?></h3>
                    <p class="acf-wheel-modal-desc">
                        <?php esc_html_e( 'You need to be logged in to save custom wheels to your library and generate permanent public share links.', 'acf-spin-wheel' ); ?>
                    </p>
                    <div class="acf-wheel-modal-actions">
                        <a href="<?php echo esc_url( $login_url ); ?>" class="acf-wheel-btn acf-wheel-btn-primary">
                            <?php esc_html_e( 'Log In Now', 'acf-spin-wheel' ); ?>
                        </a>
                        <?php if ( get_option( 'users_can_register' ) ) : ?>
                            <a href="<?php echo esc_url( wp_registration_url() ); ?>" class="acf-wheel-btn acf-wheel-btn-outline">
                                <?php esc_html_e( 'Register Free', 'acf-spin-wheel' ); ?>
                            </a>
                        <?php endif; ?>
                        <button type="button" class="acf-wheel-btn acf-wheel-btn-secondary" id="acf-wheel-login-dismiss">
                            <?php esc_html_e( 'Continue as Guest', 'acf-spin-wheel' ); ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- MODAL: Share Link -->
            <div class="acf-wheel-modal-backdrop" id="acf-wheel-share-modal" style="display: none;" role="dialog" aria-modal="true">
                <div class="acf-wheel-modal-dialog">
                    <button type="button" class="acf-wheel-modal-close" id="acf-wheel-share-close" aria-label="<?php esc_attr_e( 'Close', 'acf-spin-wheel' ); ?>">&times;</button>
                    <div class="acf-wheel-modal-icon">🔗</div>
                    <h3 class="acf-wheel-modal-title"><?php esc_html_e( 'Share This Wheel', 'acf-spin-wheel' ); ?></h3>
                    <p class="acf-wheel-modal-desc">
                        <?php esc_html_e( 'Anyone with this unique link can spin this wheel in read-only mode:', 'acf-spin-wheel' ); ?>
                    </p>
                    <div class="acf-wheel-share-box">
                        <input type="text" id="acf-wheel-share-input" class="acf-wheel-input" readonly />
                        <button type="button" class="acf-wheel-btn acf-wheel-btn-primary" id="acf-wheel-btn-copy-modal">
                            📋 <?php esc_html_e( 'Copy', 'acf-spin-wheel' ); ?>
                        </button>
                    </div>
                    <p class="acf-wheel-share-hint"><?php esc_html_e( 'Link copied automatically when you click Copy!', 'acf-spin-wheel' ); ?></p>
                </div>
            </div>

            <!-- Toast Container -->
            <div class="acf-wheel-toasts" id="acf-wheel-toasts" aria-live="polite"></div>

        </div>
        <?php
        return ob_get_clean();
    }
}
