<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Persistence\ContentProvider;

/**
 * @author Adam Banaszkiewicz
 */
trait SymfonyContainerStandarizableTrait
{
    protected function standarizeArray(array $data): array
    {
        $result = [];

        $result['code'] = $data['code'];
        $result['type'] = $data['type'];
        $result['name'] = $data['name'];
        $result['icon'] = $data['icon'];
        $result['controller'] = $data['controller'];
        $result['is_routable'] = (bool) $data['is_routable'];
        $result['is_hierarchical'] = (bool) $data['is_hierarchical'];
        $result['routing_strategy'] = $data['routing_strategy'];
        $result['fields_groups'] = [];

        foreach ($data['layout']['sections'] as $sectionCode => $section) {
            foreach ($section['groups'] as $groupCode => $group) {
                $result['fields_groups'][] = [
                    'code' => $groupCode,
                    'section' => $sectionCode,
                    'name' => $group['name'] ?? $groupCode,
                    'fields' => $this->standarizeFields($group['fields'])
                ];
            }
        }

        return $result;
    }

    protected function standarizeFields(array $fields, ?string $parent = null): array
    {
        $result = [];

        foreach ($fields as $fieldCode => $field) {
            if ($field['parent'] !== $parent) {
                continue;
            }

            $constraints = [];
            $configuration = [];

            foreach ($field['constraints'] as $constraint) {
                $modificators = [];

                foreach ($constraint['modificators'] ?? [] as $modificator) {
                    $modificators[$modificator['code']] = $modificator['value'];
                }

                $constraints[] = [
                    'code' => $constraint['code'],
                    'modificators' => $modificators,
                ];
            }

            foreach ($field['configuration'] as $config) {
                $configuration[$config['code']] = $config['value'];
            }

            $fields[$fieldCode]['code'] = $fieldCode;
            $fields[$fieldCode]['constraints'] = $constraints;
            $fields[$fieldCode]['configuration'] = $configuration;
            $fields[$fieldCode]['children'] = $this->standarizeFields($fields, $fieldCode);

            $result[$fieldCode] = $fields[$fieldCode];
        }

        return $result;
    }
}
