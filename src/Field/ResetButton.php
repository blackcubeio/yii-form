<?php

declare(strict_types=1);

/**
 * ResetButton.php
 *
 * PHP Version 8.4
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\Form\Field;

/**
 * Button of type reset, styled by Bleet.
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class ResetButton extends AbstractButton
{
    protected function getType(): string
    {
        return 'reset';
    }
}
