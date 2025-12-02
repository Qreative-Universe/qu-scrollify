<?php
/**
 * Plugin Name: QU Scrollify Helper
 * Description: jQuery Scrollify를 간단히 적용할 수 있는 설정 페이지 + 자동 초기화 플러그인.
 * Author: QU
 * Version: 1.0.0
 * Text Domain: qu-scrollify
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class QU_Scrollify_Helper {

    private $option_key = 'qu_scrollify_settings';

    public function __construct() {
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_head', array( $this, 'output_custom_css' ), 100 );
        add_action( 'admin_head', array( $this, 'admin_styles' ) );
    }

    /**
     * 기본 설정값
     */
    public function get_default_settings() {
        return array(
            'enabled'               => 1,
            'section'               => '.scrollify-section',
            'sectionName'           => '',
            'interstitialSection'   => '',
            'easing'                => 'easeOutQuad',
            'scrollSpeed'           => 800,
            'offset'                => 0,
            'scrollbars'            => 1,
            'standardScrollElements'=> '',
            'setHeights'            => 1,
            'overflowScroll'        => 1,
            'updateHash'            => 0,
            'touchScroll'           => 1,
            'custom_css'            => '',
        );
    }

    /**
     * 설정 등록
     */
    public function register_settings() {
        register_setting(
            'qu_scrollify_settings_group',
            $this->option_key,
            array( $this, 'sanitize_settings' )
        );
    }

    /**
     * 저장 전 값 정리
     */
    public function sanitize_settings( $input ) {
        $defaults = $this->get_default_settings();
        $output   = $defaults;

        $output['enabled']             = isset( $input['enabled'] ) ? 1 : 0;
        $output['section']             = ! empty( $input['section'] ) ? sanitize_text_field( $input['section'] ) : $defaults['section'];
        $output['sectionName']         = ! empty( $input['sectionName'] ) ? sanitize_text_field( $input['sectionName'] ) : '';
        $output['interstitialSection'] = ! empty( $input['interstitialSection'] ) ? sanitize_text_field( $input['interstitialSection'] ) : '';
        $output['scrollSpeed']         = isset( $input['scrollSpeed'] ) ? intval( $input['scrollSpeed'] ) : $defaults['scrollSpeed'];
        $output['easing']              = ! empty( $input['easing'] ) ? sanitize_text_field( $input['easing'] ) : $defaults['easing'];
        $output['offset']              = isset( $input['offset'] ) ? intval( $input['offset'] ) : 0;
        $output['scrollbars']          = isset( $input['scrollbars'] ) ? 1 : 0;
        $output['standardScrollElements'] = ! empty( $input['standardScrollElements'] ) ? sanitize_text_field( $input['standardScrollElements'] ) : '';
        $output['setHeights']          = isset( $input['setHeights'] ) ? 1 : 0;
        $output['overflowScroll']      = isset( $input['overflowScroll'] ) ? 1 : 0;
        $output['updateHash']          = isset( $input['updateHash'] ) ? 1 : 0;
        $output['touchScroll']         = isset( $input['touchScroll'] ) ? 1 : 0;

        if ( isset( $input['custom_css'] ) ) {
            $output['custom_css'] = sanitize_textarea_field( $input['custom_css'] );
        } else {
            $output['custom_css'] = '';
        }

        return $output;
    }

    /**
     * 관리자 메뉴 추가
     */
    public function add_settings_page() {
        add_options_page(
            'QU Scrollify 설정',
            'QU Scrollify',
            'manage_options',
            'qu-scrollify-settings',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * 관리자 스타일 (이 페이지에서만)
     */
    public function admin_styles() {
        if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'qu-scrollify-settings' ) {
            return;
        }
        ?>
        <style>
            .qu-scrollify-wrap { max-width: 1100px; }
            .qu-scrollify-wrap h1 { margin-bottom: 6px; }
            .qu-scrollify-wrap .qu-subtitle {
                margin-top: 0;
                color: #555;
                font-size: 13px;
            }
            .qu-card {
                background: #fff;
                padding: 20px 24px;
                border: 1px solid #dcdcdc;
                border-radius: 6px;
                margin-bottom: 24px;
                box-shadow: 0 1px 2px rgba(0,0,0,.03);
            }
            .qu-card h2 { margin-top: 0; margin-bottom: 12px; }
            .qu-card .form-table th {
                width: 220px;
                padding-top: 14px;
            }
            .qu-card .form-table td {
                padding-top: 10px;
            }
            .qu-card pre {
                background: #f7f7f7;
                padding: 10px 12px;
                border-radius: 4px;
                overflow: auto;
                border: 1px solid #e3e3e3;
                font-size: 12px;
                line-height: 1.5;
            }
            .qu-card code {
                background: #f3f3f3;
                padding: 1px 4px;
                border-radius: 3px;
                font-size: 12px;
            }
            .qu-docs h2 { margin-top: 0; margin-bottom: 10px; }
            .qu-docs h3 { margin-top: 18px; margin-bottom: 6px; }
            .qu-docs p { margin: 0 0 8px; }
            .qu-docs .description { margin-top: 4px; color: #666; }
            .qu-divider { margin: 24px 0; border-top: 1px solid #e2e2e2; }
            @media (min-width: 1280px) {
                .qu-scrollify-wrap { max-width: 1200px; }
            }
        </style>
        <?php
    }

    /**
     * 설정 페이지
     */
    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $defaults = $this->get_default_settings();
        $options  = wp_parse_args( get_option( $this->option_key, array() ), $defaults );
        ?>
        <div class="wrap qu-scrollify-wrap">
            <h1>QU Scrollify 설정</h1>
            <p class="qu-subtitle">jQuery Scrollify를 워드프레스에 간단히 적용하기 위한 설정 페이지입니다.</p>

            <div class="qu-card">
                <form method="post" action="options.php">
                    <?php settings_fields( 'qu_scrollify_settings_group' ); ?>

                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row">사용 여부</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="<?php echo esc_attr( $this->option_key ); ?>[enabled]" value="1" <?php checked( $options['enabled'], 1 ); ?> />
                                    이 사이트에서 Scrollify 기능 활성화
                                </label>
                                <p class="description">
                                    체크하면 사이트 전역에서 Scrollify 스크립트가 로드됩니다.<br>
                                    실제로는 지정한 섹션 셀렉터를 가진 요소가 있을 때만 초기화됩니다.
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">section (필수)</th>
                            <td>
                                <input type="text"
                                       name="<?php echo esc_attr( $this->option_key ); ?>[section]"
                                       value="<?php echo esc_attr( $options['section'] ); ?>"
                                       class="regular-text" />
                                <p class="description">
                                    Scrollify가 적용될 섹션의 CSS 셀렉터입니다.<br>
                                    예: <code>.scrollify-section</code>, <code>section.fullpage</code> 등
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">sectionName (옵션)</th>
                            <td>
                                <input type="text"
                                       name="<?php echo esc_attr( $this->option_key ); ?>[sectionName]"
                                       value="<?php echo esc_attr( $options['sectionName'] ); ?>"
                                       class="regular-text" />
                                <p class="description">
                                    각 섹션에 <code>data-section-name</code> 속성으로 이름을 주고 싶을 때 사용합니다.<br>
                                    예: <code>&lt;section class="scrollify-section" data-section-name="about"&gt;</code>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">interstitialSection</th>
                            <td>
                                <input type="text"
                                       name="<?php echo esc_attr( $this->option_key ); ?>[interstitialSection]"
                                       value="<?php echo esc_attr( $options['interstitialSection'] ); ?>"
                                       class="regular-text" />
                                <p class="description">
                                    Scrollify 섹션 사이에 있지만, 풀페이지 스크롤 대상에서 제외하고 일반 스크롤로 남길 영역의 셀렉터입니다.<br>
                                    예: <code>.site-header, .site-footer, .no-scrollify</code>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">scrollSpeed</th>
                            <td>
                                <input type="number"
                                       name="<?php echo esc_attr( $this->option_key ); ?>[scrollSpeed]"
                                       value="<?php echo esc_attr( $options['scrollSpeed'] ); ?>"
                                       class="small-text" /> ms
                                <p class="description">
                                    섹션 이동 속도(밀리초)입니다. 기본값 800 ~ 1100 정도를 권장합니다.
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">easing</th>
                            <td>
                                <input type="text"
                                       name="<?php echo esc_attr( $this->option_key ); ?>[easing]"
                                       value="<?php echo esc_attr( $options['easing'] ); ?>"
                                       class="regular-text" />
                                <p class="description">
                                    jQuery easing 이름입니다. 기본값 <code>easeOutQuad</code>.<br>
                                    별도 easing 플러그인이 없으면 <code>swing</code> 또는 <code>linear</code>를 권장합니다.
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">offset</th>
                            <td>
                                <input type="number"
                                       name="<?php echo esc_attr( $this->option_key ); ?>[offset]"
                                       value="<?php echo esc_attr( $options['offset'] ); ?>"
                                       class="small-text" /> px
                                <p class="description">
                                    섹션 정지 위치를 위/아래로 얼마나 이동시킬지(px) 지정합니다.<br>
                                    상단에 고정 헤더가 있을 때 헤더 높이만큼 offset을 줄 수 있습니다.
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">scrollbars</th>
                            <td>
                                <label>
                                    <input type="checkbox"
                                           name="<?php echo esc_attr( $this->option_key ); ?>[scrollbars]"
                                           value="1" <?php checked( $options['scrollbars'], 1 ); ?> />
                                    브라우저 기본 스크롤바 표시
                                </label>
                                <p class="description">
                                    UX 측면에서는 보통 스크롤바를 표시(<code>true</code>)하는 것이 좋습니다.
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">standardScrollElements</th>
                            <td>
                                <input type="text"
                                       name="<?php echo esc_attr( $this->option_key ); ?>[standardScrollElements]"
                                       value="<?php echo esc_attr( $options['standardScrollElements'] ); ?>"
                                       class="regular-text" />
                                <p class="description">
                                    Scrollify 섹션 안에 있지만, 내부 스크롤이 필요해 Scrollify가 가로채면 안 되는 요소의 셀렉터입니다.<br>
                                    예: <code>.scrollable-div, .faq-inner</code>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">setHeights</th>
                            <td>
                                <label>
                                    <input type="checkbox"
                                           name="<?php echo esc_attr( $this->option_key ); ?>[setHeights]"
                                           value="1" <?php checked( $options['setHeights'], 1 ); ?> />
                                    setHeights 활성화
                                </label>
                                <p class="description">
                                    Scrollify가 섹션 높이를 자동으로 맞출지 여부입니다. 보통 켜두고, CSS에서 <code>min-height: 100vh;</code>와 병행합니다.
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">overflowScroll</th>
                            <td>
                                <label>
                                    <input type="checkbox"
                                           name="<?php echo esc_attr( $this->option_key ); ?>[overflowScroll]"
                                           value="1" <?php checked( $options['overflowScroll'], 1 ); ?> />
                                    섹션 내부 오버플로우 스크롤 허용
                                </label>
                                <p class="description">
                                    섹션 콘텐츠가 섹션 높이를 넘칠 때, 해당 섹션 안에서 스크롤을 허용할지 여부입니다.
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">updateHash</th>
                            <td>
                                <label>
                                    <input type="checkbox"
                                           name="<?php echo esc_attr( $this->option_key ); ?>[updateHash]"
                                           value="1" <?php checked( $options['updateHash'], 1 ); ?> />
                                    URL 해시 업데이트
                                </label>
                                <p class="description">
                                    섹션 이동 시 주소창의 해시(#)를 업데이트할지 여부입니다.<br>
                                    <code>sectionName</code>과 함께 사용하면 <code>/#about</code> 형태의 딥링크를 만들 수 있습니다.
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">touchScroll</th>
                            <td>
                                <label>
                                    <input type="checkbox"
                                           name="<?php echo esc_attr( $this->option_key ); ?>[touchScroll]"
                                           value="1" <?php checked( $options['touchScroll'], 1 ); ?> />
                                    모바일/터치 스크롤 허용
                                </label>
                                <p class="description">
                                    모바일/터치 디바이스에서 스와이프/터치 스크롤을 허용할지 여부입니다.
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">Custom CSS (옵션)</th>
                            <td>
                                <textarea
                                    name="<?php echo esc_attr( $this->option_key ); ?>[custom_css]"
                                    rows="8"
                                    class="large-text code"
                                ><?php echo esc_textarea( $options['custom_css'] ); ?></textarea>
                                <p class="description">
                                    사이트 전역에 적용할 CSS를 입력하세요. Scrollify 섹션 스타일을 이곳에서 바로 조정할 수 있습니다.
                                </p>

                                <p><strong>예시:</strong></p>
                                <pre><code>.scrollify-section {
                                    min-height: 100vh;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                }</code></pre>
                            </td>
                        </tr>

                    </table>

                    <?php submit_button(); ?>
                </form>
            </div>

            <div class="qu-card">
                <h2>현재 적용되는 Scrollify 초기화 코드</h2>
                <p>아래는 현재 설정값으로 생성되는 <code>$.scrollify()</code> 코드 예시입니다.</p>

                <pre><code>$.scrollify({
                    section: "<?php echo esc_html( $options['section'] ); ?>",
                    sectionName: "<?php echo esc_html( $options['sectionName'] ); ?>",
                    interstitialSection: "<?php echo esc_html( $options['interstitialSection'] ); ?>",
                    easing: "<?php echo esc_html( $options['easing'] ); ?>",
                    scrollSpeed: <?php echo intval( $options['scrollSpeed'] ); ?>,
                    offset: <?php echo intval( $options['offset'] ); ?>,
                    scrollbars: <?php echo $options['scrollbars'] ? 'true' : 'false'; ?>,
                    standardScrollElements: "<?php echo esc_html( $options['standardScrollElements'] ); ?>",
                    setHeights: <?php echo $options['setHeights'] ? 'true' : 'false'; ?>,
                    overflowScroll: <?php echo $options['overflowScroll'] ? 'true' : 'false'; ?>,
                    updateHash: <?php echo $options['updateHash'] ? 'true' : 'false'; ?>,
                    touchScroll: <?php echo $options['touchScroll'] ? 'true' : 'false'; ?>

                    // 콜백(before, after 등)은 기본값(빈 함수)으로 사용 중입니다.
                });</code></pre>
                <p class="description">
                    실제 프론트에서는 jQuery가 준비된 후 위와 같은 옵션으로 Scrollify가 자동 실행됩니다.<br>
                    (단, 해당 페이지에 <code><?php echo esc_html( $options['section'] ); ?></code> 셀렉터에 해당하는 요소가 있을 때만 동작)
                </p>
            </div>

            <div class="qu-card qu-docs">
                <h2>Scrollify Configuration 옵션 요약</h2>
                <p>주요 옵션들을 한글로 정리한 요약본입니다. 실제 프로젝트에서는 필요한 것만 선택적으로 사용하는 것을 권장합니다.</p>

                <h3><code>section</code></h3>
                <p><strong>필수. 섹션 셀렉터</strong> – Scrollify가 한 화면으로 취급할 요소의 CSS 셀렉터입니다.</p>

                <h3><code>sectionName</code></h3>
                <p>각 섹션에 <code>data-section-name</code> 속성으로 이름을 부여할 때 사용하는 키입니다. <code>updateHash</code>와 함께 사용하면 <code>/#about</code>처럼 딥링크가 가능합니다.</p>

                <h3><code>interstitialSection</code></h3>
                <p>Scrollify 섹션 사이에 위치하지만, 풀페이지 스크롤 대상에서 제외하고 일반 스크롤로 남길 영역의 셀렉터입니다. 예: <code>.site-header, .site-footer</code></p>

                <h3><code>scrollSpeed</code></h3>
                <p>섹션 이동 속도(밀리초). 보통 600~1100ms 사이가 자연스럽습니다.</p>

                <h3><code>easing</code></h3>
                <p>섹션 스크롤 애니메이션 easing 함수 이름입니다. <code>easeOutExpo</code>, <code>swing</code>, <code>linear</code> 등을 사용할 수 있습니다.</p>

                <h3><code>offset</code></h3>
                <p>섹션이 멈추는 위치를 px 단위로 보정합니다. 상단 고정 헤더가 있을 때 헤더 높이만큼 offset을 주면 유용합니다.</p>

                <h3><code>scrollbars</code></h3>
                <p>브라우저 기본 스크롤바 표시 여부입니다. 대부분의 경우 <code>true</code>를 추천합니다.</p>

                <h3><code>standardScrollElements</code></h3>
                <p>섹션 내부에 있지만, 내부 스크롤이 필요해 Scrollify가 가로채면 안 되는 요소의 셀렉터입니다. 예: 긴 FAQ, 코드 박스, 내부 스크롤 영역 등.</p>

                <h3><code>setHeights</code></h3>
                <p>Scrollify가 섹션 높이를 자동으로 맞출지 여부입니다. <code>min-height: 100vh;</code>와 함께 사용하면 풀페이지 느낌을 만들기 좋습니다.</p>

                <h3><code>overflowScroll</code></h3>
                <p>섹션 콘텐츠가 섹션 높이를 넘칠 경우, 해당 섹션 안에서 스크롤을 허용할지 여부입니다.</p>

                <h3><code>updateHash</code></h3>
                <p>섹션 이동 시 URL 해시(#)를 업데이트할지 여부입니다. 딥링크/뒤로가기 동작과 연동할 수 있습니다.</p>

                <h3><code>touchScroll</code></h3>
                <p>모바일·터치 디바이스에서 스와이프/터치 스크롤을 허용할지 여부입니다.</p>

                <h3>콜백: <code>before</code>, <code>after</code>, <code>afterResize</code>, <code>afterRender</code></h3>
                <p>이 플러그인 버전에서는 콜백은 기본값(빈 함수)으로 사용하며, 고급 커스터마이징이 필요할 경우 코드 레벨에서 확장하는 것을 권장합니다.</p>
            </div>
        </div>
        <?php
    }

    /**
     * 프론트 스크립트 로딩 및 초기화
     */
    public function enqueue_scripts() {
        $defaults = $this->get_default_settings();
        $options  = wp_parse_args( get_option( $this->option_key, array() ), $defaults );

        if ( empty( $options['enabled'] ) ) {
            return;
        }

        wp_enqueue_script( 'jquery' );

        wp_enqueue_script(
            'jquery-scrollify',
            'https://cdnjs.cloudflare.com/ajax/libs/jquery-scrollify/1.0.21/jquery.scrollify.min.js',
            array( 'jquery' ),
            '1.0.21',
            true
        );

        $section        = esc_js( $options['section'] );
        $sectionName    = esc_js( $options['sectionName'] );
        $interstitial   = esc_js( $options['interstitialSection'] );
        $scrollSpeed    = intval( $options['scrollSpeed'] );
        $easing         = esc_js( $options['easing'] );
        $offset         = intval( $options['offset'] );
        $scrollbars     = $options['scrollbars'] ? 'true' : 'false';
        $standardEls    = esc_js( $options['standardScrollElements'] );
        $setHeights     = $options['setHeights'] ? 'true' : 'false';
        $overflowScroll = $options['overflowScroll'] ? 'true' : 'false';
        $updateHash     = $options['updateHash'] ? 'true' : 'false';
        $touchScroll    = $options['touchScroll'] ? 'true' : 'false';

        $inline_js = <<<JS
jQuery(function($){
    if ( $("{$section}").length ) {
        $.scrollify({
            section: "{$section}",
            sectionName: "{$sectionName}",
            interstitialSection: "{$interstitial}",
            easing: "{$easing}",
            scrollSpeed: {$scrollSpeed},
            offset: {$offset},
            scrollbars: {$scrollbars},
            standardScrollElements: "{$standardEls}",
            setHeights: {$setHeights},
            overflowScroll: {$overflowScroll},
            updateHash: {$updateHash},
            touchScroll: {$touchScroll}
        });
    }
});
JS;

        wp_add_inline_script( 'jquery-scrollify', $inline_js );
    }

    /**
     * 커스텀 CSS 출력
     */
    public function output_custom_css() {
        $defaults = $this->get_default_settings();
        $options  = wp_parse_args( get_option( $this->option_key, array() ), $defaults );

        if ( empty( $options['enabled'] ) ) {
            return;
        }

        if ( empty( $options['custom_css'] ) ) {
            return;
        }

        $css = trim( $options['custom_css'] );
        if ( $css === '' ) {
            return;
        }

        echo "\n<style id='qu-scrollify-custom-css'>\n" . $css . "\n</style>\n";
    }
}

new QU_Scrollify_Helper();
