<?php

declare(strict_types=1);

namespace Muffe\EnumConstraint\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EnumValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Enum) {
            throw new UnexpectedTypeException($constraint, Enum::class);
        }

        $enumType = $constraint->enumType;

        if (!\is_a($enumType, \UnitEnum::class, true)) {
            throw new \RuntimeException('The "enumType" option of the Enum constraint should be a Enum.');
        }

        if (null === $value) {
            return;
        }

        $property = 'name';
        if (\is_a($enumType, \BackedEnum::class, true)) {
            $property = 'value';
        }

        $enumCases = \array_map(static fn ($item) => $item->{$property}, $enumType::cases());

        if (!$constraint->message) {
            $constraint->message = 'The enum case "{{ value }}" is not valid. Valid cases are: "{{ choices }}"';
        }

        if ($constraint->multiple) {
            if (!\is_array($value)) {
                $value = [$value];
            }

            foreach ($value as $singleValue) {
                if (\in_array($singleValue, $enumCases, true)) {
                    continue;
                }

                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $this->formatValue($singleValue))
                    ->setParameter('{{ choices }}', $this->formatValues($enumCases))
                    ->setCode(Enum::NO_SUCH_CASE_ERROR)
                    ->setInvalidValue($value)
                    ->addViolation();

                return;
            }

            return;
        }

        if (\in_array($value, $enumCases, true)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $this->formatValue($value))
            ->setParameter('{{ choices }}', $this->formatValues($enumCases))
            ->setCode(Enum::NO_SUCH_CASE_ERROR)
            ->setInvalidValue($value)
            ->addViolation();
    }
}
