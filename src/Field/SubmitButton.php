<?php

declare(strict_types=1);

/**
 * SubmitButton.php
 *
 * PHP Version 8.4
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\Form\Field;

/**
 * Button of type submit, styled by Bleet.
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class SubmitButton extends AbstractButton
{
    protected function getType(): string
    {
        return 'submit';
    }
}
