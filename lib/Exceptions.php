<?

namespace TagMaker;

class TagMakerException extends \Exception {};

/**
 * Thrown when tries to create an element with blank tag
 */
class BlankTagException extends TagMakerException {};

/**
 * Thrown when tries to add an attribute that already exists in an element
 */
class ExistentAttributeException extends TagMakerException {};

/**
 * Thrown when tries to access a non-existent attribute
 */
class UndefinedAttributeException extends TagMakerException {};