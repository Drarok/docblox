<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category  DocBlox
 * @package   Parser\Exporter\Xml
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

/**
 * Exports a constant element's attributes and properties to a child of the given
 * parent.
 *
 * @category DocBlox
 * @package  Parser\Exporter\Xml
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Parser_Exporter_Xml_Constant
{
    /**
     * Exports the given reflection object to the parent XML element.
     *
     * This method creates a new child element on the given parent XML element
     * and takes the properties of the Reflection argument and sets the
     * elements and attributes on the child.
     *
     * If a child DOMElement is provided then the properties and attributes are
     * set on this but the child element is not appended onto the parent. This
     * is the responsibility of the invoker. Essentially this means that the
     * $parent argument is ignored in this case.
     *
     * @param DOMElement                  $parent   The parent element to augment.
     * @param DocBlox_Reflection_Constant $constant The data source.
     * @param DOMElement                  $child    Optional: child element to use
     *     instead of creating a new one on the $parent.
     *
     * @return void
     */
    public function export(
        DOMElement $parent, DocBlox_Reflection_Constant $constant,
        DOMElement $child = null
    ) {
        if (!$constant->getName()) {
            return;
        }

        if (!$child) {
            $child = new DOMElement('constant');
            $parent->appendChild($child);
        }

        $child->setAttribute('namespace', $constant->getNamespace());
        $child->setAttribute('line', $constant->getLineNumber());


        $child->appendChild(new DOMElement('name', $constant->getName()));
        $value = new DOMElement('value');
        $child->appendChild($value);
        $value->appendChild(
            $child->ownerDocument->createCDATASection($constant->getValue())
        );

        $object = new DocBlox_Parser_Exporter_Xml_DocBlock();
        $object->export($child, $constant);
    }
}