<?php

declare(strict_types=1);

/*
 * This file is part of Contao Validation Bundle.
 *
 * @author 2biased <2biased@proton.me>
 *
 * @license LGPL-3.0-or-later
 */

namespace TwoBiased\ContaoValidationBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Widget;

class AddCustomRegexpListener
{
    #[AsHook('addCustomRegexp')]
    public function __invoke(string $regexp, mixed $input, Widget $widget): bool
    {
        if ('iban' === $regexp) {
            if (verify_iban($input)) {
                if ($widget->ibanAllowedCountryCodes) { // @phpstan-ignore-line
                    $countryCodes = explode(',', (string) $widget->ibanAllowedCountryCodes);

                    if (!\in_array(iban_get_country_part($input), $countryCodes, true)) {
                        $widget->addError(sprintf($GLOBALS['TL_LANG']['ERR']['ibanRestrictedCountryCodes'], implode(', ', $countryCodes)));
                    }
                }
            } else {
                $widget->addError($GLOBALS['TL_LANG']['ERR']['iban']);
            }

            return true;
        }

        return false;
    }
}
