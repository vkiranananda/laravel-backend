<?php

namespace Backend\Root\Form\Services;


class FormField
{
    static public function getFieldEditorBlock($data, $type, $templates = [])
    {
        $text = '';
        if ($type === 'editor-blocks' && is_array($data['blocks']) && count($data['blocks']) > 0) {

            foreach ($data['blocks'] as $block) {
                $block['classes'] = '';
                if (isset($block['tunes']['TextAlign']['alignment'])) {
                    switch ($block['tunes']['TextAlign']['alignment'] ?? 'left') {
                        case 'center':
                            $block['classes'] = ' text-center';
                            break;
                        case 'right':
                            $block['classes'] = ' text-end';
                            break;
                        default:
                            break;
                    }
                }
                $text .= view($templates[$block['type']] ?? 'Form::fields.editor-blocks.' . $block['type'], ['data' => $block])->render();
            }
        }
        return $text;
    }
}
