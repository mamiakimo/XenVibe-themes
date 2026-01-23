# XenForo LESS Variables Reference

## Core Color Variables

### Text Colors
| Variable | Usage | Notes |
|----------|-------|-------|
| `@xf-textColor` | Primary text | Adapts to dark/light mode |
| `@xf-textColorMuted` | Secondary text, hints | Lower contrast |
| `@xf-textColorDimmed` | Very subtle text | Even lower contrast |

### Link Colors
| Variable | Usage |
|----------|-------|
| `@xf-linkColor` | Links, active states, primary actions |
| `@xf-linkColorHover` | Link hover state |

### Background Colors
| Variable | Usage |
|----------|-------|
| `@xf-pageBg` | Page background |
| `@xf-contentBg` | Content areas, cards |
| `@xf-contentHighlightBg` | Hover states, selected items |
| `@xf-paletteNeutral1` | Subtle background variations |
| `@xf-paletteNeutral2` | Slightly darker neutral |

### Border Colors
| Variable | Usage |
|----------|-------|
| `@xf-borderColor` | Standard borders |
| `@xf-borderColorHeavy` | Emphasized borders |
| `@xf-borderColorLight` | Subtle borders |

---

## Using fade() Function

The `fade()` function creates semi-transparent colors:

```less
// 50% opacity of page background
background: fade(@xf-pageBg, 50%);

// 10% opacity of text color (for subtle tints)
background: fade(@xf-textColor, 10%);

// 80% opacity of link color
color: fade(@xf-linkColor, 80%);
```

### Common Patterns
```less
// Glass effect background
background: fade(@xf-textColor, 8%);

// Subtle hover background
&:hover {
  background: fade(@xf-textColor, 5%);
}

// Semi-transparent overlay
background: fade(@xf-pageBg, 50%);

// Soft shadow
box-shadow: 0 2px 8px fade(@xf-textColor, 10%);
```

---

## Typography Variables

| Variable | Usage |
|----------|-------|
| `@xf-fontSizeNormal` | Base font size |
| `@xf-fontSizeSmaller` | Small text |
| `@xf-fontSizeLarger` | Large text |
| `@xf-fontFamily` | Primary font family |

---

## Spacing Variables

| Variable | Usage |
|----------|-------|
| `@xf-paddingMedium` | Standard padding |
| `@xf-paddingLarge` | Larger padding |
| `@xf-borderRadiusMedium` | Standard radius |
| `@xf-borderRadiusSmall` | Small radius |

---

## Block Variables

| Variable | Usage |
|----------|-------|
| `@xf-blockBg` | Block background |
| `@xf-blockBorderColor` | Block borders |
| `@xf-blockHeaderBg` | Block header background |

---

## Form Variables

| Variable | Usage |
|----------|-------|
| `@xf-inputBg` | Input background |
| `@xf-inputBorderColor` | Input border |
| `@xf-inputDisabledBg` | Disabled input |

---

## Button Variables

| Variable | Usage |
|----------|-------|
| `@xf-buttonBg` | Default button background |
| `@xf-buttonBgHover` | Button hover |
| `@xf-buttonPrimaryBg` | Primary button |
| `@xf-buttonPrimaryBgHover` | Primary button hover |

---

## Dark Mode Behavior

XenForo variables automatically adapt to dark/light mode:

| Variable | Light Mode | Dark Mode |
|----------|------------|-----------|
| `@xf-textColor` | Dark (#333) | Light (#fff) |
| `@xf-pageBg` | Light (#f5f5f5) | Dark (#1a1a2e) |
| `@xf-contentBg` | White (#fff) | Dark (#1e293b) |

### Why No Hardcoded Colors?

```less
// BAD - Looks wrong in dark mode
.element {
  color: #333333;
  background: #ffffff;
}

// GOOD - Adapts automatically
.element {
  color: @xf-textColor;
  background: @xf-contentBg;
}
```

---

## Custom Variables (XenVibe)

Define in `xv_variables.less`:

```less
// Card styling
@xv-card-bg: @xf-contentBg;
@xv-card-radius: 1rem;
@xv-card-padding: 1.25rem;

// Glass effect
@xv-glass-bg: fade(@xf-textColor, 8%);
@xv-glass-border: fade(@xf-textColor, 10%);
@xv-glass-blur: 8px;

// Shadows
@xv-shadow-sm: 0 1px 2px fade(@xf-textColor, 5%);
@xv-shadow-md: 0 4px 6px fade(@xf-textColor, 10%);
@xv-shadow-lg: 0 10px 15px fade(@xf-textColor, 15%);
```

---

## Accessing Style Properties

Style properties defined in Admin panel:

```less
// Access property value
.element {
  background: @property-xv_headerBackground;
}

// With fallback
.element {
  background: @property-xv_headerBackground, @xf-contentBg;
}
```

---

## Variable Debugging

To check what value a variable holds:

```less
// Temporarily add visible output
.debug::before {
  content: "@{xf-textColor}"; // Shows actual value
  position: fixed;
  top: 0;
  left: 0;
  background: yellow;
  color: black;
  padding: 5px;
  z-index: 99999;
}
```
