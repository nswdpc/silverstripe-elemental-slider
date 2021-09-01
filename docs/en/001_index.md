# Documentation

## Element

The element contains a title, HTML content and a link for the entire element.

In your template, the `HomepageHero` field can be used to define a different type of slider (example: a 'hero' slider on the home page).

## Slide

A slide can contain:

1. Title
1. Text (no html)
1. Image + width/height hint
1. A link


Once slides are added to the element, they can be sorted via drag/drop under the 'Slides' tab.

Configuration for the slide:
1. `default_thumb_width` the default width of a thumbnail image
1. `default_thumb_height` the default height of a thumbnail image
1. `allowed_file_types ` allowed upload types for images

## Adding requirements

The module is unopinionated about requirements. To provide your own requirements/slider implementation:

### Add an extension

```php
<?php

namespace My\Project\Extensions;

use SilverStripe\View\Requirements;
use SilverStripe\Core\Extension;

/**
 * Provide requirements for a slider
 */
class ElementSliderExtension extends Extension
{

    /**
     * Add requirements
     */
    public function sliderRequirements()
    {

        Requirements::javascript('https://example.com/some/js/requirement.js');

    }

}
```

### Apply the configuration 

Add project configuration, in the standard Silverstripe way.

```yaml
NSWDPC\Elemental\Models\Slider\ElementSlider:
  extensions:
    - 'My\Project\Extensions\Extensions\ElementSliderExtension'
```

### Modify HTML templates

Override the following templates in your project or project theme to use your own HTML.

```shell
templates/Includes/SlideImage.ss -> a slide image
templates/NSWDPC/Elemental/Models/Slider.ss -> the slider HTML
```
