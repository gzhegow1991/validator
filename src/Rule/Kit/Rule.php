<?php

namespace Gzhegow\Validator\Rule\Kit;

use Gzhegow\Validator\Rule\RuleInterface;


class Rule implements RuleDefinitionInterface
{
    /**
     * @return array<class-string<RuleInterface>, bool|RuleInterface>
     */
    public static function rules() : array
    {
        return [
            \Gzhegow\Validator\Rule\Kit\Implicit\BlankRule::class,
            \Gzhegow\Validator\Rule\Kit\Implicit\NotBlankRule::class,
            \Gzhegow\Validator\Rule\Kit\Implicit\PresentRule::class,
            \Gzhegow\Validator\Rule\Kit\Implicit\NotPresentRule::class,
            \Gzhegow\Validator\Rule\Kit\Implicit\PresentAnyRule::class,
            \Gzhegow\Validator\Rule\Kit\Implicit\PresentedWithoutAllRule::class,
            \Gzhegow\Validator\Rule\Kit\Implicit\PresentPairRule::class,
            \Gzhegow\Validator\Rule\Kit\Implicit\PresentedWithOneRule::class,
            \Gzhegow\Validator\Rule\Kit\Implicit\PresentSideRule::class,
            \Gzhegow\Validator\Rule\Kit\Implicit\PresentedWithoutOneRule::class,
            \Gzhegow\Validator\Rule\Kit\Implicit\PresentSetRule::class,
            \Gzhegow\Validator\Rule\Kit\Implicit\PresentedWithAllRule::class,
            //
            \Gzhegow\Validator\Rule\Kit\Main\Arr\DiffAllRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Arr\DiffAnyRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Arr\IntersectAllRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Arr\IntersectAnyRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Arr\KeysDiffAllRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Arr\KeysDiffAnyRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Arr\KeysIntersectAllRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Arr\KeysIntersectAnyRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Arr\UniqueRule::class,
            //
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\Field\DateEqFieldRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\Field\DateNeqFieldRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\Field\DateGtFieldRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\Field\DateLtFieldRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\Field\DateMaxFieldRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\Field\DateMinFieldRule::class,
            //
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\DateBetweenRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\DateInsideRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\DateEqRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\DateNeqRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\DateGtRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\DateLtRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\DateMaxRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\DateMinRule::class,
            //
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Size\SizeBetweenRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Size\SizeMaxRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Size\SizeMinRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Size\SizeRule::class,
            //
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\Field\EqFieldRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\Field\NeqFieldRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\Field\GtFieldRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\Field\LtFieldRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\Field\LteFieldRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\Field\GteFieldRule::class,
            //
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\BetweenRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\InsideRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\EqRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\NeqRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\GtRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\LtRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\LteRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\GteRule::class,
            //
            \Gzhegow\Validator\Rule\Kit\Main\Format\JsonRule::class,
            //
            \Gzhegow\Validator\Rule\Kit\Main\In\Field\InFieldRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\In\Field\InNotFieldRule::class,
            //
            \Gzhegow\Validator\Rule\Kit\Main\In\InEnumRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\In\InNotEnumRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\In\InNotRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\In\InRule::class,
            //
            \Gzhegow\Validator\Rule\Kit\Main\Net\IpInSubnetsRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Net\IpInSubnetsV4Rule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Net\IpInSubnetsV6Rule::class,
            //
            \Gzhegow\Validator\Rule\Kit\Main\Obj\IsOfARule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Obj\IsOfClassRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Obj\IsOfSubclassRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Obj\StructIsARule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Obj\StructIsClassRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Obj\StructIsSubclassRule::class,
            //
            \Gzhegow\Validator\Rule\Kit\Main\Str\ContainsRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Str\CtypeAlnumRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Str\CtypeAlphaRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Str\CtypeDigitRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Str\EndsRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Str\RegexNotRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Str\RegexRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\Str\StartsRule::class,
            //
            \Gzhegow\Validator\Rule\Kit\Main\ArrayRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\DictRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\GettypeRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\ListRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\ObjectRule::class,
            \Gzhegow\Validator\Rule\Kit\Main\UuidRule::class,
            //
            \Gzhegow\Validator\Rule\Kit\Type\Date\DateRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\Date\DateTzRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\Date\DateTzNamedRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\Date\DateTzOffsetRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\Date\IntervalRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\Date\TimezoneNamedRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\Date\TimezoneOffsetRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\Date\TimezoneRule::class,
            //
            \Gzhegow\Validator\Rule\Kit\Type\File\FileRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\File\ImageRule::class,
            //
            \Gzhegow\Validator\Rule\Kit\Type\Net\AddressIpRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\Net\AddressIpV4Rule::class,
            \Gzhegow\Validator\Rule\Kit\Type\Net\AddressIpV6Rule::class,
            \Gzhegow\Validator\Rule\Kit\Type\Net\AddressMacRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\Net\SubnetRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\Net\SubnetV4Rule::class,
            \Gzhegow\Validator\Rule\Kit\Type\Net\SubnetV6Rule::class,
            //
            \Gzhegow\Validator\Rule\Kit\Type\Social\EmailRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\Social\PhoneRealRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\Social\PhoneRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\Social\TelRealRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\Social\TelRule::class,
            //
            \Gzhegow\Validator\Rule\Kit\Type\Url\HostRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\Url\LinkRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\Url\UrlRule::class,
            //
            \Gzhegow\Validator\Rule\Kit\Type\BoolRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\BooleanRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\DecimalRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\DoubleRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\FloatRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\IntRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\IntegerRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\NumRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\NumericFloatRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\NumericIntRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\NumericRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\StringRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\TrimRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\UserboolFalseRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\UserboolRule::class,
            \Gzhegow\Validator\Rule\Kit\Type\UserboolTrueRule::class,
        ];
    }


    public static function blank(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Implicit\BlankRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function not_blank(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Implicit\NotBlankRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function present(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Implicit\PresentRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function not_present(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Implicit\NotPresentRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function present_any(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Implicit\PresentAnyRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function presented_without_all(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Implicit\PresentedWithoutAllRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function present_pair(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Implicit\PresentPairRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function presented_with_one(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Implicit\PresentedWithOneRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function present_side(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Implicit\PresentSideRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function presented_without_one(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Implicit\PresentedWithoutOneRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function present_set(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Implicit\PresentSetRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function presented_with_all(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Implicit\PresentedWithAllRule::class,
            [ 'parameters' => $parameters ]
        );
    }



    public static function diff_all(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Arr\DiffAllRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function diff_any(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Arr\DiffAnyRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function intersect_all(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Arr\IntersectAllRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function intersect_any(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Arr\IntersectAnyRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function keys_diff_all(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Arr\KeysDiffAllRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function keys_diff_any(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Arr\KeysDiffAnyRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function keys_intersect_all(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Arr\KeysIntersectAllRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function keys_intersect_any(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Arr\KeysIntersectAnyRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function unique(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Arr\UniqueRule::class,
            [ 'parameters' => $parameters ]
        );
    }



    public static function date_eq_field(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\Field\DateEqFieldRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function date_neq_field(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\Field\DateNeqFieldRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function date_gt_field(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\Field\DateGtFieldRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function date_lt_field(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\Field\DateLtFieldRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function date_max_field(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\Field\DateMaxFieldRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function date_min_field(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\Field\DateMinFieldRule::class,
            [ 'parameters' => $parameters ]
        );
    }


    public static function date_between(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\DateBetweenRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function date_inside(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\DateInsideRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function date_eq(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\DateEqRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function date_neq(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\DateNeqRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function date_gt(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\DateGtRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function date_lt(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\DateLtRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function date_max(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\DateMaxRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function date_min(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\DateMinRule::class,
            [ 'parameters' => $parameters ]
        );
    }



    public static function size_between(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Size\SizeBetweenRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function size_max(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Size\SizeMaxRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function size_min(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Size\SizeMinRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function size(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Size\SizeRule::class,
            [ 'parameters' => $parameters ]
        );
    }



    public static function eq_field(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\Field\EqFieldRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function neq_field(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\Field\NeqFieldRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function gt_field(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\Field\GtFieldRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function lt_field(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\Field\LtFieldRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function lte_field(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\Field\LteFieldRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function gte_field(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\Field\GteFieldRule::class,
            [ 'parameters' => $parameters ]
        );
    }


    public static function between(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\BetweenRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function inside(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\InsideRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function eq(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\EqRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function neq(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\NeqRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function gt(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\GtRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function lt(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\LtRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function lte(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\LteRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function gte(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\GteRule::class,
            [ 'parameters' => $parameters ]
        );
    }



    public static function json(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Format\JsonRule::class,
            [ 'parameters' => $parameters ]
        );
    }



    public static function in_field(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\In\Field\InFieldRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function in_not_field(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\In\Field\InNotFieldRule::class,
            [ 'parameters' => $parameters ]
        );
    }


    public static function in_enum(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\In\InEnumRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function in_not_enum(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\In\InNotEnumRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function in_not(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\In\InNotRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function in(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\In\InRule::class,
            [ 'parameters' => $parameters ]
        );
    }



    public static function ip_in_subnets(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Net\IpInSubnetsRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function ip_in_subnets_v4(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Net\IpInSubnetsV4Rule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function ip_in_subnets_v6(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Net\IpInSubnetsV6Rule::class,
            [ 'parameters' => $parameters ]
        );
    }



    public static function is_of_a(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Obj\IsOfARule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function is_of_class(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Obj\IsOfClassRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function is_of_subclass(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Obj\IsOfSubclassRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function struct_is_a(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Obj\StructIsARule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function struct_is_class(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Obj\StructIsClassRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function struct_is_subclass(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Obj\StructIsSubclassRule::class,
            [ 'parameters' => $parameters ]
        );
    }



    public static function contains(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Str\ContainsRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function ctype_alnum(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Str\CtypeAlnumRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function ctype_alpha(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Str\CtypeAlphaRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function ctype_digit(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Str\CtypeDigitRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function ends(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Str\EndsRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function regex_not(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Str\RegexNotRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function regex(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Str\RegexRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function starts(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\Str\StartsRule::class,
            [ 'parameters' => $parameters ]
        );
    }



    public static function array(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\ArrayRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function dict(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\DictRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function gettype(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\GettypeRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function list(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\ListRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function object(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\ObjectRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function uuid(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Main\UuidRule::class,
            [ 'parameters' => $parameters ]
        );
    }



    public static function date(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Date\DateRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function date_tz(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Date\DateTzRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function date_tz_named(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Date\DateTzNamedRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function date_tz_offset(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Date\DateTzOffsetRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function interval(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Date\IntervalRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function timezone_named(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Date\TimezoneNamedRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function timezone_offset(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Date\TimezoneOffsetRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function timezone(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Date\TimezoneRule::class,
            [ 'parameters' => $parameters ]
        );
    }



    public static function file(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\File\FileRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function image(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\File\ImageRule::class,
            [ 'parameters' => $parameters ]
        );
    }



    public static function address_ip(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Net\AddressIpRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function address_ip_v4(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Net\AddressIpV4Rule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function address_ip_v6(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Net\AddressIpV6Rule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function address_mac(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Net\AddressMacRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function subnet(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Net\SubnetRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function subnet_v4(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Net\SubnetV4Rule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function subnet_v6(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Net\SubnetV6Rule::class,
            [ 'parameters' => $parameters ]
        );
    }



    public static function email(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Social\EmailRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function phone_real(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Social\PhoneRealRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function phone(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Social\PhoneRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function tel_real(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Social\TelRealRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function tel(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Social\TelRule::class,
            [ 'parameters' => $parameters ]
        );
    }



    public static function host(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Url\HostRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function link(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Url\LinkRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function url(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\Url\UrlRule::class,
            [ 'parameters' => $parameters ]
        );
    }



    public static function boolean(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\BooleanRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function bool(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\BoolRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function decimal(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\DecimalRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function double(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\DoubleRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function float(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\FloatRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function integer(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\IntegerRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function int(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\IntRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function numeric(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\NumericRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function numeric_int(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\NumericIntRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function numeric_float(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\NumericFloatRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function num(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\NumRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function string(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\StringRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function trim(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\TrimRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function userbool_false(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\UserboolFalseRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function userbool(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\UserboolRule::class,
            [ 'parameters' => $parameters ]
        );
    }

    public static function userbool_true(array $parameters = [])
    {
        return \Gzhegow\Validator\Rule\GenericRule::fromClassAndParameters(
            \Gzhegow\Validator\Rule\Kit\Type\UserboolTrueRule::class,
            [ 'parameters' => $parameters ]
        );
    }
}
