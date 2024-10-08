<?php

namespace XmlMiddleware;

use JsonSerializable;
use SimpleXMLElement;

class TypedXMLElement extends SimpleXMLElement implements JsonSerializable
{
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): mixed
    {
        $array = [];

        // json encode attributes if any.
        if ($attributes = $this->attributes()) {
            $array['@attributes'] = iterator_to_array($attributes);
        }

        // json encode child elements if any. group on duplicate names as an array.
        foreach ($this as $name => $element) {
            if (isset($array[$name])) {
                if (!is_array($array[$name])) {
                    $array[$name] = [$array[$name]];
                }
                $array[$name][] = $element;
            } else {
                $array[$name] = $element;
            }
        }

        // JSON-encode non-whitespace element simplexml text values.
        // Preserve whitespace in non-empty strings.
        $text = (string) $this;
        $trimmed = trim($text);
        if (strlen($trimmed)) {
            if ($array) {
                $array['@text'] = $text;
            } else {
                $array = $text;
            }
        }

        // return empty elements as NULL (self-closing or empty tags)
        if ($array === []) {
            $array = '';
        }

        return $array;
    }
}
