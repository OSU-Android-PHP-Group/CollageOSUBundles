<?php
namespace OSU\CollageAPIBundle\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Bytea datatype for PostgreSQL
 */

class MyType extends Type
{
    const BYTEA= 'bytea'; // modify to match your type name

    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        // return the SQL used to create your column type. To create a portable column type, use the $platform.
        return self::BYTEA;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        // This is executed when the value is read from the database. Make your conversions here, optionally using the $platform.
        return pg_unescape_bytea($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        // This is executed when the value is written to the database. Make your conversions here, optionally using the $platform.
        return pg_escape_bytea($value);
    }

    public function getName()
    {
        return self::BYTEA; // modify to match your constant name
    }
}

