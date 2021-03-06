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
 *
 *
 * @category DocBlox
 * @package  Parser\Exporter\Xml
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Parser_Exporter_Xml_File
{
    public $include_source = false;

    public function export(DOMElement $parent, DocBlox_Reflection_File $file) {
        $child = new DOMElement('file');
        $parent->appendChild($child);

        $child->setAttribute('path', ltrim($file->getFilename(), './'));
        $child->setAttribute('hash', $file->getHash());

        $object = new DocBlox_Parser_Exporter_Xml_DocBlock();
        $object->export($child, $file);

        // add markers
        if (count($file->getMarkers()) > 0) {
            $markers = new DOMElement('markers');
            $child->appendChild($markers);

            foreach ($file->getMarkers() as $marker) {
                $marker_obj = new DOMElement(
                    strtolower($marker[0]),
                    htmlspecialchars(trim($marker[1]))
                );
                $markers->appendChild($marker_obj);
                $marker_obj->setAttribute('line', $marker[2]);
            }
        }

        if (count($file->getParseErrors()) > 0) {
            $parse_errors = new DOMElement('parse_markers');
            $child->appendChild($parse_errors);

            foreach ($file->getParseErrors() as $error) {
                $marker_obj = new DOMElement(
                    strtolower($error[0]),
                    htmlspecialchars(trim($error[1]))
                );
                $parse_errors->appendChild($marker_obj);
                $marker_obj->setAttribute('line', $error[2]);
                $marker_obj->setAttribute('code', $error[3]);
            }
        }

        // add namespace aliases
        foreach ($file->getNamespaceAliases() as $alias => $namespace) {
            $alias_obj = new DOMElement('namespace-alias', $namespace);
            $child->appendChild($alias_obj);
            $alias_obj->setAttribute('name', $alias);
        }

        /** @var DocBlox_Reflection_Include $include */
        foreach ($file->getIncludes() as $include) {
            $object = new DocBlox_Parser_Exporter_Xml_Include();
            $object->export($child, $include);
        }

        /** @var DocBlox_Reflection_Constant $constant */
        foreach ($file->getConstants() as $constant) {
            $constant->setDefaultPackageName($file->getDefaultPackageName());
            $object = new DocBlox_Parser_Exporter_Xml_Constant();
            $object->export($child, $constant);
        }

        /** @var DocBlox_Reflection_Function $function */
        foreach ($file->getFunctions() as $function) {
            $function->setDefaultPackageName($file->getDefaultPackageName());
            $object = new DocBlox_Parser_Exporter_Xml_Function();
            $object->export($child, $function);
        }

        /** @var DocBlox_Reflection_Interface $interface */
        foreach ($file->getInterfaces() as $interface) {
            $interface->setDefaultPackageName($file->getDefaultPackageName());
            $object = new DocBlox_Parser_Exporter_Xml_Interface();
            $object->export($child, $interface);
        }

        /** @var DocBlox_Reflection_Class $class */
        foreach ($file->getClasses() as $class) {
            $class->setDefaultPackageName($file->getDefaultPackageName());
            $object = new DocBlox_Parser_Exporter_Xml_Class();
            $object->export($child, $class);
        }

        // if we want to include the source for each file; append a new
        // element 'source' which contains a compressed, encoded version
        // of the source
        if ($this->include_source) {
            $child->appendChild(
                new DOMElement(
                    'source',
                    base64_encode(gzcompress($file->getContents()))
                )
            );
        }
    }
}