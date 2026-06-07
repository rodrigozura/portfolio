---
name: Blog Personal
description: Cinematic academic portfolio system for a PHP and MariaDB personal blog.
colors:
  ink-night: "#121820"
  charcoal-panel: "#1B222B"
  slate-panel: "#222B35"
  text-primary: "#EEF2F4"
  text-secondary: "#B7C1C9"
  border-muted: "#707B85"
  accent-cyan: "#0F8FBD"
  danger: "#C85252"
  success: "#4FA77A"
  overlay-strong: "#070B0FCC"
typography:
  display:
    fontFamily: "Montserrat, Poppins, Arial, sans-serif"
    fontSize: "clamp(3.5rem, 8vw, 6rem)"
    fontWeight: 900
    lineHeight: 0.95
    letterSpacing: "-0.045em"
  headline:
    fontFamily: "Montserrat, Poppins, Arial, sans-serif"
    fontSize: "clamp(2rem, 4vw, 3rem)"
    fontWeight: 800
    lineHeight: 1.05
    letterSpacing: "-0.03em"
  title:
    fontFamily: "Montserrat, Poppins, Arial, sans-serif"
    fontSize: "1.25rem"
    fontWeight: 800
    lineHeight: 1.2
    letterSpacing: "-0.015em"
  body:
    fontFamily: "Montserrat, Poppins, Arial, sans-serif"
    fontSize: "1rem"
    fontWeight: 400
    lineHeight: 1.55
    letterSpacing: "normal"
  label:
    fontFamily: "Montserrat, Poppins, Arial, sans-serif"
    fontSize: "0.75rem"
    fontWeight: 700
    lineHeight: 1
    letterSpacing: "0.16em"
rounded:
  xs: "6px"
  sm: "8px"
  md: "12px"
  pill: "999px"
spacing:
  xs: "0.5rem"
  sm: "0.75rem"
  md: "1rem"
  lg: "1.5rem"
  xl: "2rem"
  section: "clamp(4rem, 9vw, 8rem)"
components:
  button-primary:
    backgroundColor: "{colors.accent-cyan}"
    textColor: "{colors.ink-night}"
    rounded: "{rounded.pill}"
    padding: "0.75rem 1.5rem"
    typography: "{typography.body}"
  button-secondary:
    backgroundColor: "transparent"
    textColor: "{colors.text-primary}"
    rounded: "{rounded.pill}"
    padding: "0.75rem 1.5rem"
    typography: "{typography.body}"
  card-publication:
    backgroundColor: "{colors.charcoal-panel}"
    textColor: "{colors.text-primary}"
    rounded: "{rounded.md}"
    padding: "1.5rem"
  input-field:
    backgroundColor: "{colors.slate-panel}"
    textColor: "{colors.text-primary}"
    rounded: "{rounded.sm}"
    padding: "0.75rem 1rem"
    typography: "{typography.body}"
---

# Design System: Blog Personal

## 1. Overview

**Creative North Star: "The Night Desk Portfolio"**

This system turns an academic PHP blog into a cinematic personal portfolio. The public site should feel like a focused desk at night: dark surfaces, one decisive cyan accent, strong sans-serif type, and personal photography used as structure rather than decoration.

The design is not a generic academic template. It should preserve the final practical assignment requirements while presenting them with the confidence of a compact portfolio: a dramatic hero, lean navigation, framed publication areas, and a dashboard that feels related to the public site without sacrificing clarity.

**Key Characteristics:**

- Dark, image-led, high-contrast surfaces.
- One restrained cyan accent used sparingly.
- Strong sans-serif hierarchy with heavy display titles.
- Thin-line framing instead of heavy card shadows.
- Public pages are airy; dashboard pages are denser but still controlled.
- Responsive behavior must be explicit for mobile, tablet, and desktop.

**The Portfolio Discipline Rule.** The site can feel cinematic, but it still serves an academic deliverable. Every visual choice must support personal presentation, publication browsing, admin clarity, or final report access.

## 2. Colors

The palette is a restrained dark system: tinted near-black neutrals, softened white text, and a single cyan accent that appears only where attention or state needs it.

### Primary

- **Ink Night**: root page background and full-screen sections. It sets the cinematic base.
- **Accent Cyan**: active nav item, focus ring, section underline, selected state, publication tag, and primary action emphasis.

### Secondary

- **Charcoal Panel**: dashboard panels, publication containers, login form surfaces, and admin list rows.
- **Slate Panel**: input fields, secondary surfaces, empty states, and tonal separation inside the dashboard.

### Tertiary

- **Danger Red**: delete actions, destructive confirmation text, and deletion error states only.
- **Success Green**: save confirmations and positive status messages only.

### Neutral

- **Text Primary**: main copy, headings over dark backgrounds, and button labels on outline actions.
- **Text Secondary**: descriptions, metadata, dates, helper text, and low-emphasis labels.
- **Border Muted**: thin dividers, corner frames, input borders, and low-emphasis outlines.
- **Overlay Strong**: image overlay for hero and portrait sections so text remains legible.

### Named Rules

**The Cyan Scarcity Rule.** Accent Cyan must stay below roughly 10 percent of any screen. Its rarity makes active states and calls to action readable.

**The No Pure Black Rule.** Never use pure black or pure white. The system relies on tinted darks and softened whites to avoid flat, harsh contrast.

**The Danger Isolation Rule.** Danger Red appears only for destructive actions and their confirmation states. Do not use it as decoration.

## 3. Typography

**Display Font:** Montserrat or Poppins with Arial fallback.

**Body Font:** Montserrat or Poppins with Arial fallback.

**Label Font:** Same family, uppercase only for short labels.

**Character:** The type is geometric, direct, and portfolio-like. Weight contrast does the work: heavy display headings, medium labels, and readable body copy.

### Hierarchy

- **Display**: 900 weight, `clamp(3.5rem, 8vw, 6rem)`, 0.95 line-height. Used only for the hero name or the strongest page-level identity statement.
- **Headline**: 800 weight, `clamp(2rem, 4vw, 3rem)`, 1.05 line-height. Used for major public sections and dashboard page titles.
- **Title**: 800 weight, 1.25rem, 1.2 line-height. Used for publication titles, dashboard cards, and timeline-like blocks.
- **Body**: 400 weight, 1rem, 1.55 line-height. Used for descriptions, publication excerpts, helper text, and paragraphs. Keep long text under 75 characters per line.
- **Label**: 700 weight, 0.75rem, 0.16em letter-spacing. Used for nav-active markers, tags, status labels, and short uppercase section labels.

### Named Rules

**The Heavy Name Rule.** The hero identity may use very heavy type. Everything else must step down so the page does not become visually loud everywhere.

**The Label Restraint Rule.** Uppercase tracked labels are allowed, but not above every section. Use them for state, navigation, and one or two high-value section markers.

## 4. Elevation

This design is flat by default. Depth comes from photography, overlays, thin borders, tonal panels, and generous spacing. Shadows should be nearly invisible and never become the main separator.

### Shadow Vocabulary

- **Panel Lift**: `0 18px 60px rgba(3, 8, 13, 0.28)`. Use only for login and dashboard panels that need separation from the page background.
- **Interactive Lift**: `0 10px 28px rgba(15, 143, 189, 0.16)`. Use only on focused or hovered primary actions when extra feedback is needed.

### Named Rules

**The Border Before Shadow Rule.** Use a thin border or tonal shift before adding shadow. If a panel needs a large black shadow to read, the composition is wrong.

**The Photograph Carries Depth Rule.** Public pages should use image contrast and overlay direction to create depth. Do not fake depth with stacked cards.

## 5. Components

### Buttons

- **Shape:** pill-shaped, fully rounded (`999px`).
- **Primary:** Accent Cyan fill, Ink Night text, medium weight, short labels only. Use for the main action on a screen.
- **Secondary:** transparent fill, Text Primary, 1px Border Muted outline. Use for secondary navigation such as report download or login.
- **Hover / Focus:** border or background shifts to Accent Cyan, slight translate up or active press down, visible focus ring in Accent Cyan.
- **Rules:** Button labels must fit on one line. Do not duplicate the same CTA intent with multiple labels.

### Chips

- **Style:** transparent or Charcoal Panel background, 1px Border Muted outline, Accent Cyan text for category emphasis.
- **State:** selected or active chips may use a subtle Accent Cyan border. Do not fill chips with bright cyan unless they are acting as a primary filter.
- **Use cases:** publication category, date metadata, dashboard status, seed-data marker.

### Cards / Containers

- **Corner Style:** medium radius (`12px`) for dashboard panels; public publication items may use thin corner-frame treatment.
- **Background:** Charcoal Panel for admin surfaces, Ink Night for public section backgrounds.
- **Shadow Strategy:** flat by default, Panel Lift only for login or focused dashboard containers.
- **Border:** 1px Border Muted, cyan only on hover or focus.
- **Internal Padding:** 1.5rem for cards, 2rem for major panels.
- **Signature Pattern:** corner-frame publication cards inspired by the portfolio reference. The frame should imply structure without boxing every item heavily.

### Inputs / Fields

- **Style:** Slate Panel background, Border Muted stroke, 8px radius, Text Primary value text, Text Secondary helper text.
- **Focus:** Accent Cyan border and visible focus ring. No glow-heavy neon treatment.
- **Error:** Danger Red border and inline message below the field.
- **Disabled:** lower contrast, never hidden. Disabled text must remain readable.
- **Rules:** Labels sit above inputs. Placeholder text is never the only label.

### Navigation

- **Desktop:** top horizontal navigation, 56 to 64px height, single line, left-aligned items, optional discrete login/report action on the right.
- **Active State:** Accent Cyan text or underline.
- **Hover State:** transition to Accent Cyan with no heavy background pill.
- **Mobile:** compact menu or stacked top navigation. Public links must remain reachable without layout wrapping.

### Hero Cover

- **Structure:** full-bleed image or dark photographic surface, Overlay Strong, left-aligned identity block, short subtext, one primary CTA and one secondary CTA.
- **Text Limits:** hero subtext max 20 words. Headline max two lines on desktop.
- **Image Role:** personal image is structural. Do not reduce it to a small avatar if a full hero composition is possible.

### Publication Grid

- **Public Layout:** desktop 2-column grid or asymmetric 1+2 rhythm, mobile single column.
- **Content:** title, category chip, date, short excerpt, and optional action link.
- **Hover:** frame/border intensifies toward Accent Cyan, content lifts subtly.
- **Empty State:** composed dark panel explaining that publications will appear after dashboard updates.

### Dashboard Panel

- **Layout:** denser than public pages, but still dark, clear, and aligned.
- **Actions:** create, edit, delete, and logout must be visually distinct.
- **Delete:** destructive action requires confirmation before deletion.
- **Feedback:** validation errors inline, success messages contextual, no generic spinner as first choice.

## 6. Do's and Don'ts

### Do:

- **Do** use a dark cinematic base with softened white text and one cyan accent.
- **Do** make the Home feel like a visual cover, not a flat list of requirements.
- **Do** use the personal image as a structural part of the hero or About layout.
- **Do** keep the dashboard functional: labels above inputs, visible errors, clear admin actions.
- **Do** expose local design expectations in tickets: hover, active, focus, empty, error, and delete confirmation states.
- **Do** keep public visitors read-only and make admin-only actions visually protected behind the dashboard.
- **Do** collapse complex layouts to one column below 768px.

### Don't:

- **Don't** use AI-purple gradients, gradient text, or neon glow decoration.
- **Don't** use pure black or pure white.
- **Don't** build the public site as a generic academic white page.
- **Don't** use identical icon-card grids as the main content structure.
- **Don't** bury the report PDF link or dashboard login in a crowded footer.
- **Don't** expose credentials, personal sensitive data, or admin mutation controls on public pages.
- **Don't** use a SaaS-style four-column footer. This is a personal academic portfolio, not a marketing platform.
- **Don't** animate layout properties. Use opacity and transform only, and respect reduced-motion preferences.
