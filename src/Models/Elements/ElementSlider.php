<?php

namespace NSWDPC\Elemental\Models\Slider;

use DNADesign\Elemental\Models\ElementContent;
use gorriecoe\Link\Models\Link;
use gorriecoe\LinkField\LinkField;
use SilverStripe\LinkField\Models\Link as CoreLink;
use SilverStripe\LinkField\Form\LinkField as CoreLinkField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\CheckboxField;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * ElementSlider adds a content slider via a sortable upload field
 * @property bool $HomepageHero
 * @property int $HeroLinkID
 * @method \gorriecoe\Link\Models\Link HeroLink()
 * @method \SilverStripe\LinkField\Models\Link CoreHeroLink()
 * @property int $CoreHeroLinkID
 * @method \SilverStripe\ORM\HasManyList<\NSWDPC\Elemental\Models\Slider\Slide> Slides()
 */
class ElementSlider extends ElementContent
{
    /**
     * @inheritdoc
     */
    private static string $icon = 'font-icon-picture';

    /**
     * @inheritdoc
     */
    private static bool $inline_editable = false;

    /**
     * @inheritdoc
     */
    private static string $table_name = 'ElementSlider';

    /**
     * @inheritdoc
     */
    private static string $title = 'Content slider';

    /**
     * @inheritdoc
     */
    private static string $class_description = "Display one or more slides with optional hero";

    /**
     * @inheritdoc
     */
    private static string $singular_name = 'Slider';

    /**
     * @inheritdoc
     */
    private static string $plural_name = 'Sliders';

    /**
     * @inheritdoc
     */
    #[\Override]
    public function getType()
    {
        return _t(self::class . '.BlockType', 'Content slider');
    }

    /**
     * @inheritdoc
     */
    private static array $db = [
        'HomepageHero' => 'Boolean'
    ];

    /**
     * @inheritdoc
     */
    private static array $has_one = [
        'HeroLink' => Link::class,
        'CoreHeroLink' => CoreLink::class
    ];

    /**
     * @inheritdoc
     */
    private static array $has_many = [
        'Slides' => Slide::class,
    ];

    /**
     * @inheritdoc
     */
    private static array $owns = [
        'Slides',
        'CoreHeroLink'
    ];

    private static array $cascade_deletes = [
        'CoreHeroLink'
    ];

    private static array $cascade_duplicates = [
        'CoreHeroLink'
    ];

    /**
     * @inheritDoc
     */
    #[\Override]
    public function forTemplate($holder = true): string
    {
        $this->addSliderRequirements();
        return parent::forTemplate($holder);
    }

    /**
     * Provides an extension method 'sliderRequirements' where Requirements can
     * be added to handle slider content
     */
    protected function addSliderRequirements()
    {
        $this->extend('sliderRequirements');
    }

    /**
     * @inheritdoc
     */
    #[\Override]
    public function getCMSFields()
    {
        $fields = parent::getCmsFields();
        $fields->removeByName(['HeroLinkID']);

        $fields->addFieldToTab(
            'Root.Main',
            CheckboxField::create(
                'HomepageHero',
                _t(
                    self::class . 'HOMEPAGE_HERO',
                    "The context for this slider is a 'hero'"
                )
            )
        );

        $fields->removeByName(['HeroLinkID', 'HeroLink', 'CoreHeroLink']);
        $fields->addFieldToTab(
            'Root.Main',
            $this->getLinkField()
                ->setDescription(_t(
                    self::class . 'HOMEPAGE_HERO_LINK_DESCRIPTION',
                    "An optional link to display on the slider, as a 'call to action'"
                ))
        );

        if ($this->isInDB()) {
            $field = GridField::create(
                'Slides',
                _t(
                    self::class . 'SLIDES',
                    'Slides'
                ),
                $this->Slides(),
                GridFieldConfig_RelationEditor::create()
            );
            $config = $field->getConfig();
            $config->addComponent(GridFieldOrderableRows::create('Sort'));
            $fields->addFieldToTab(
                'Root.Slides',
                $field
            );
        }

        return $fields;
    }

    /**
     * Return the field used to render the link
     * based on migration state
     */
    protected function getLinkField(): CoreLinkField|LinkField
    {
        $link = $this->getDerivedHeroLink();
        if ($link instanceof CoreLink) {
            return CoreLinkField::create('CoreHeroLink', _t(self::class . 'LINK', 'Link'));
        }

        return LinkField::create('HeroLink', _t(self::class . 'LINK', 'Link'), $this);
    }

    /**
     * Template method
     */
    public function DerivedHeroLink(): CoreLink|Link
    {
        return $this->getDerivedHeroLink();
    }

    /**
     * Returns the relevant link based on migration status
     */
    public function getDerivedHeroLink(): CoreLink|Link
    {
        $coreLink = $this->CoreHeroLink();
        /** @var Link $legacyLink */
        $legacyLink = $this->getComponent('HeroLink');
        if ($coreLink->isInDB() || !$legacyLink->isInDB()) {
            return $coreLink;
        }

        return $legacyLink;
    }

    /**
     * @inheritdoc
     */
    public function SortedSlides(): \SilverStripe\ORM\DataList
    {
        return $this->Slides()->Sort('Sort');
    }

}
