# enum-constraint
A Symfony Validator constraint that validates if given strings are valid cases in a given PHP 8 Enum

```
enum ContactCategory: string
{
    case Child           = 'Child';
    case Grandchild      = 'Grandchild';
}

use Muffe\EnumConstraint\Constraints\Enum;

#[
    Enum(
        enumType: ContactCategory::class,
    )
]
public ?string $category = null;
```

Validates that $category contains a string that is equal to the backed value of one of the given enum cases, or the name of the case if no backed values are given.

```
use Muffe\EnumConstraint\Constraints\Enum;

#[
    Enum(
        enumType: ContactCategory::class,
        multiple: true
    )
]
public ?array $categories = null;
```
Also supports validating multiple given values
