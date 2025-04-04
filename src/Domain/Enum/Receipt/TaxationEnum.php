<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Domain\Enum\Receipt;

enum TaxationEnum: string
{
    case OSN = 'osn';
    case USN_INCOME = 'usn_income';
    case USN_INCOME_OUTCOME = 'usn_income_outcome';
    case ENVD = 'envd';
    case ESN = 'esn';
    case PATENT = 'patent';

    public function getDescription(): string
    {
        return match($this) {
            self::OSN                => 'Общая система налогообложения',
            self::USN_INCOME         => 'Упрощенная СН (доходы)',
            self::USN_INCOME_OUTCOME => 'Упрощенная СН (доходы минус расходы)',
            self::ENVD               => 'Единый налог на вмененный доход',
            self::ESN                => 'Единый сельскохозяйственный налог',
            self::PATENT             => 'Патентная система налогообложения'
        };
    }
}
