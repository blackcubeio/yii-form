<?php

declare(strict_types=1);

/**
 * Radio.php
 *
 * PHP Version 8.4
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\Form\Field;

use Blackcube\Form\Traits\AttributesTrait;
use Blackcube\Form\Traits\FieldDataTrait;
use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;

/**
 * Radio widget - radio button for forms
 *
 * Usage:
 *   Radio::widget()->name('plan')->value('small')->label('Small')->render()
 *   Radio::widget()->name('plan')->value('medium')->label('Medium')->description('8 GB RAM...')->render()
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class Radio extends Widget
{
    use AttributesTrait;
    use FieldDataTrait;

    private ?string $name = null;
    private ?string $value = null;
    protected bool $checked = false;
    protected bool $disabled = false;
    protected bool $required = false;
    private string|Label|null $label = null;
    private ?string $description = null;

    /**
     * Sets the name
     */
    public function name(string $name): self
    {
        $new = clone $this;
        $new->name = $name;
        return $new;
    }

    /**
     * Sets the value
     */
    public function value(string $value): self
    {
        $new = clone $this;
        $new->value = $value;
        return $new;
    }

    /**
     * Selects the radio
     */
    public function checked(bool $checked = true): self
    {
        $new = clone $this;
        $new->checked = $checked;
        return $new;
    }

    /**
     * Disables the radio
     */
    public function disabled(bool $disabled = true): self
    {
        $new = clone $this;
        $new->disabled = $disabled;
        return $new;
    }

    /**
     * Marks comme requis
     */
    public function required(bool $required = true): self
    {
        $new = clone $this;
        $new->required = $required;
        return $new;
    }

    /**
     * Sets the label
     */
    public function label(string|Label $label): self
    {
        $new = clone $this;
        $new->label = $label;
        return $new;
    }

    /**
     * Sets the description (enables description mode)
     */
    public function description(string $description): self
    {
        $new = clone $this;
        $new->description = $description;
        return $new;
    }

    /**
     * Renders the radio
     */
    public function render(): string
    {
        if ($this->description !== null) {
            return $this->renderWithDescription();
        }

        return $this->renderSimple();
    }

    /**
     * Render simple : label inline
     */
    private function renderSimple(): string
    {
        $inputHtml = $this->renderRadioInput();

        $labelHtml = $this->renderLabelText();

        return Html::tag(
            'label',
            $inputHtml.$labelHtml,
            ['class' => implode(' ', $this->wrapperClasses())]
        )->encode(false)->render();
    }

    /**
     * Renders with description
     */
    private function renderWithDescription(): string
    {
        $inputHtml = $this->renderRadioInput();

        $inputWrapper = Html::div(
            $inputHtml,
            ['class' => 'flex h-6 items-center']
        )->encode(false)->render();

        $labelHtml = $this->renderLabelForDescription();
        $descriptionHtml = $this->renderDescription();

        $textWrapper = Html::div(
            $labelHtml.$descriptionHtml,
            ['class' => 'ml-3 text-sm/6']
        )->encode(false)->render();

        return Html::div(
            $inputWrapper.$textWrapper,
            ['class' => 'relative flex items-start']
        )->encode(false)->render();
    }

    /**
     * Render the radio input
     */
    private function renderRadioInput(): string
    {
        $name = $this->name;
        $tagAttrs = $this->getTagAttributes();
        $id = $tagAttrs['id'] ?? null;
        $modelValue = null;
        $checked = $this->checked || ($this->value !== null && $modelValue === $this->value);
        $required = $this->required;

        $defaults = [
            'type' => 'radio',
            'class' => implode(' ', $this->getInputClasses()),
        ];

        if ($name !== null) {
            $defaults['name'] = $name;
        }

        if ($id !== null) {
            $defaults['id'] = $id;
        }

        if ($this->value !== null) {
            $defaults['value'] = $this->value;
        }

        if ($checked) {
            $defaults['checked'] = true;
        }

        if ($this->disabled === true) {
            $defaults['disabled'] = true;
        }

        if ($required) {
            $defaults['required'] = true;
        }

        if ($this->description !== null && $id !== null) {
            $defaults['aria-describedby'] = $id.'-description';
        }

        $attributes = $this->prepareTagAttributes($defaults);

        $attributes = [...$attributes, ...$this->getFieldDataAttributes()];

        return Html::input('radio', $name, $this->value)
            ->attributes($attributes)
            ->render();
    }

    /**
     * Renders the label text (mode simple)
     */
    private function renderLabelText(): string
    {
        $labelContent = null;
        if ($this->label instanceof Label) {
            $labelContent = $this->label->getContent();
        } elseif ($this->label !== null) {
            $labelContent = $this->label;
        }

        if ($labelContent === null || $labelContent === '') {
            return '';
        }

        $required = $this->required;
        $innerHtml = Html::encode($labelContent);
        if ($required) {
            $innerHtml .= Html::tag('span', ' *', ['class' => $this->requiredMarkClasses()])->encode(false);
        }

        return Html::span($innerHtml, [
            'class' => $this->labelTextClasses(),
        ])->encode(false)->render();
    }

    /**
     * Renders the label (description mode)
     */
    private function renderLabelForDescription(): string
    {
        $labelContent = null;
        if ($this->label instanceof Label) {
            $labelContent = $this->label->getContent();
        } elseif ($this->label !== null) {
            $labelContent = $this->label;
        }

        if ($labelContent === null || $labelContent === '') {
            return '';
        }

        $labelWidget = new Label($labelContent);

        $id = $this->getTagAttributes()['id'] ?? null;
        if ($id !== null) {
            $labelWidget = $labelWidget->for($id);
        }

        if ($this->required === true) {
            $labelWidget = $labelWidget->required();
        }

        return $labelWidget->render();
    }

    /**
     * Renders the description
     */
    private function renderDescription(): string
    {
        $description = $this->description;

        if ($description === null || $description === '') {
            return '';
        }

        $tagAttrs = $this->getTagAttributes();
        $id = $tagAttrs['id'] ?? null;

        $attributes = [
            'class' => $this->descriptionClasses(),
        ];

        if ($id !== null) {
            $attributes['id'] = $id.'-description';
        }

        return Html::p($description, $attributes)->render();
    }

    /**
     * Base classes (unused, point d extension)
     * @return string[]
     */
    protected function prepareClasses(): array
    {
        return [];
    }

    /**
     * Classes of the label wrapping the radio button.
     *
     * @return string[]
     */
    protected function wrapperClasses(): array
    {
        return [];
    }

    /**
     * Classes of the label text.
     */
    protected function labelTextClasses(): string
    {
        return '';
    }

    /**
     * Classes of the asterisk marking a required field.
     */
    protected function requiredMarkClasses(): string
    {
        return '';
    }

    /**
     * Classes of the text describing the option.
     */
    protected function descriptionClasses(): string
    {
        return '';
    }

    /**
     * Classes for the radio input
     * @return string[]
     */
    protected function getInputClasses(): array
    {
        return [];
    }

    /**
     * Color classes of the input (toggle states with dot)
     * @return string[]
     */
    protected function getInputColorClasses(): array
    {
        return [];
    }
}
