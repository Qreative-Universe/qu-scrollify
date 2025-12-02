# QU Scrollify Helper

### A lightweight WordPress plugin for applying jQuery Scrollify with a visual admin UI.

`QU Scrollify Helper`는 jQuery Scrollify를 **워드프레스 사이트 어디서든 손쉽게 적용**할 수 있도록 만든 경량 플러그인입니다.
풀페이지 스크롤(Fullpage-like), 섹션 단위 스냅 이동, 인터랙션 중심의 랜딩 페이지 제작 시 매우 효과적입니다.

개발자가 직접 JS 코드를 삽입하거나 테마 파일을 수정할 필요 없이,
**설정 페이지에서 옵션을 입력하는 것만으로 Scrollify 전체 기능을 제어**할 수 있습니다.

---

## ✨ 주요 특징 (Key Features)

### ✔ WordPress 관리자 화면에서 모든 Scrollify 설정 제어

Scrollify의 핵심 옵션을 대부분 지원합니다:

* `section`
* `sectionName`
* `interstitialSection`
* `easing`
* `scrollSpeed`
* `offset`
* `scrollbars`
* `standardScrollElements`
* `setHeights`
* `overflowScroll`
* `updateHash`
* `touchScroll`

옵션 입력 → 저장 → 자동 초기화까지 **코드 작성 없이** 완성됩니다.

---

### ✔ 자동 초기화 (No coding required)

설정값을 기반으로 플러그인이 다음과 같은 코드를 자동 생성합니다:

```js
$.scrollify({
  section: ".scrollify-section",
  scrollSpeed: 800,
  easing: "easeOutQuad",
  ...
});
```

설정 화면 하단에는 **현재 적용되는 초기화 코드 예시**가 표시되어
개발자·기획자 모두 구성 상태를 바로 확인할 수 있습니다.

---

### ✔ Custom CSS 입력 필드 제공

사이트 전체에 적용될 CSS를
WordPress 관리자 → QU Scrollify 설정에서 직접 추가할 수 있습니다.

```css
.scrollify-section {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
```

풀페이지 레이아웃을 제어하거나, 섹션 단위 스타일링을 할 때 매우 유용합니다.

---

### ✔ interstitialSection 및 overflowScroll 완벽 지원

* **interstitialSection**: 헤더/푸터/특정 영역은 기본 스크롤 유지
* **overflowScroll**: 섹션 내부 콘텐츠가 넘칠 때 자체 스크롤 허용

Scrollify의 고급 옵션들까지 WordPress형 UI로 정리하여
개발자 경험(DX)을 최적화했습니다.

---

### ✔ GeneratePress, Elementor, Gutenberg 등 모든 테마와 호환

Scrollify의 구조적 특성상,
어떤 테마나 페이지 빌더 환경에서도 무리 없이 사용할 수 있습니다.

특히:

* GenerateBlocks Container
* Gutenberg Group / Section
* Elementor Section

등에 클래스를 붙이는 것만으로 즉시 스크롤 인터랙션 구현이 가능합니다.

---

## 🧩 설치 방법 (Installation)

1. 플러그인 폴더 생성:

```bash
/wp-content/plugins/qu-scrollify/
```

2. 파일 업로드:

```bash
/wp-content/plugins/qu-scrollify/qu-scrollify.php
```

3. WordPress 관리자 → 플러그인 → QU Scrollify Helper → **활성화**

4. 설정:

```text
관리자 → 설정 → QU Scrollify
```

---

## ⚙️ 사용 방법 (How to Use)

### 1) 섹션에 클래스 추가

Scrollify를 적용할 섹션에 아래 클래스(또는 설정에서 지정한 클래스)를 추가합니다:

```html
<div class="scrollify-section"> ... </div>
```

### 2) Custom CSS 또는 테마 CSS에서 기본 높이 설정

```css
.scrollify-section {
    min-height: 100vh;
}
```

### 3) 관리자 화면에서 옵션 조정

Scrollify의 모든 주요 동작을 시각적으로 제어할 수 있습니다.

---

## 📸 설정 화면 미리보기 (Admin UI Preview)

> (GitHub 저장소에 실제 스크린샷을 `screenshot-*.png` 형식으로 업로드해 사용하면 됩니다.)

---

## 🧪 호환성 (Compatibility)

* WordPress 5.0+
* PHP 7.4+
* jQuery가 활성화된 모든 테마
* GeneratePress / Astra / Block Themes / Elementor / Bricks / Beaver Builder 등과 완전 호환

---

## 📦 향후 확장 계획 (Roadmap)

* Scrollify Callback 함수 UI 추가 (`before`, `after`, `afterResize`, `afterRender`)
* 특정 페이지에서만 Scrollify 실행 옵션
* Breakpoint 기반 "모바일에서 Scrollify 끄기" 기능
* Multi-section transition animations 확장

필요 시 요청에 따라 빠르게 추가 가능합니다.

---

## 📝 라이선스 (License)

본 플러그인은 MIT License를 따르며 자유롭게 사용·수정·배포할 수 있습니다.
Scrollify 라이브러리 자체는 원저작자 라이선스를 따릅니다.
