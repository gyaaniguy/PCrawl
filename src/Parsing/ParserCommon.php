<?php

namespace Gyaaniguy\PCrawl\Parsing;

use QueryPath\CSS\ParseException;

class ParserCommon extends ParserBase
{
    /**
     * @throws ParseException
     */
    public function getAllLinks(): array
    {
        $links = [];
        foreach ($this->querypath->find('a') as $l) {
            $links[] = ['href' => $l->attr('href'), 'text' => $l->text()];
        }
        return $links;
    }

    /**
     * @throws ParseException
     */
    public function getAllImages(): array
    {
        $images = [];
        foreach ($this->querypath->find('img') as $l) {
            $images[] = ['src' => $l->attr('src'), 'alt' => $l->attr('alt')];
        }
        return $images;
    }

    /**
     * @throws ParseException
     */
    public function getAllFormInputDetails(): array
    {
        $forms = [];
        foreach ($this->querypath->find('form') as $f) {
            $inputs = [];
            $row = ['action' => $f->attr('action'), 'method' => $f->attr('method')];
            $formInputs = $f->find('input');
            if (count($formInputs) > 0) {
                foreach ($formInputs as $formInput) {
                    $inputs[] = [
                        'name' => $formInput->attr('name'),
                        'value' => $formInput->attr('value'),
                        'type' => $formInput->attr('type')
                    ];
                }
                if (!empty($inputs)) {
                    $row['inputs'] = $inputs;
                }
            }
            $forms[] = $row;
        }
        return $forms;
    }
}
