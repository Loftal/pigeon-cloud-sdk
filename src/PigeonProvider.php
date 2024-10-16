<?php

namespace PigeonCloudSdk;

class PigeonProvider
{
    private string $table_id;
    private string $table_key;
    private PigeonGateway $pigeonGateway;
    public function __construct(string $table_id, bool $useSession = false)
    {
        $this->table_id = $table_id;
        $this->table_key = ctype_digit($table_id)? 'table_id' : 'table';
        if ($useSession) {
            if (!isset($_SESSION['pigeon_user']['auth'])) {
                throw new \Exception('Pigeon User No Auth Error.');
            }
            $auth = $_SESSION['pigeon_user']['auth'];
        } else {
            $auth = PigeonUtil::getMasterAuth();
        }
        $this->pigeonGateway = new PigeonGateway($auth);
    }

    public function setDebug(bool $debug = true): void
    {
        $this->pigeonGateway->setDebug($debug);
    }

    public function getFormDataTemplate(): array
    {
        return [$this->table_key => $this->table_id];
    }

    public function showFields(): void
    {
        $response = $this->getFields();
        echo "<pre>";
        var_dump($response);
        echo "</pre>";
        exit(0);
    }

    public function getFields(): ?array
    {
        $form_data = $this->getFormDataTemplate();
        $response = $this->pigeonGateway->getFields($form_data);
        if (isset($response['data'])) {
            return $response['data'];
        }
        return null;
    }

    private function toPostData(array $datas): array
    {
        $form_data = $this->getFormDataTemplate();
        foreach ($datas as $i => $data) {
            foreach ($data as $key => $val) {
                if( is_a($val, 'CURLFile') ) {
                    $form_data["{$key}[{$i}]"] = $val;
                } elseif (is_array($val)) {
                    foreach ($val as $j => $v) {
                        $form_data["data[{$i}][{$key}][{$j}]"] = $v;
                    }
                } else {
                    $form_data["data[{$i}][{$key}]"] = $val;
                }
            }
        }
        return $form_data;
    }

    private function setViewData($data){
        if(!$data || count($data) == 0){
            return [];
        }
        if( !isset($data[0]) ) {
            $view_data = $data['view_data'];
            $view_data['raw_data'] = $data['raw_data'];
            return $view_data;
        } else {
            foreach ($data as &$d) {
                $d = $this->setViewData($d);
            }
            unset($d);
            return $data;
        }
    }

    public function fetch(?PigeonCondition $pigeonCondition = null, int $page = 1, int $per_page = 30, ?string $order = null): array
    {
        $form_data = $this->getFormDataTemplate();
        if ($pigeonCondition) {
            $form_data['condition'] = $pigeonCondition->toArray();
        }
        $form_data['limit'] = $per_page;
        $form_data['offset'] = $per_page * ($page - 1);
        if (!empty($order)) {
            $form_data['order'] = $order;
        }
        $response = $this->pigeonGateway->getRecord($form_data);
        if ($response['result']!='success') {
            throw new \Exception('Pigeon result error.');
        }
        $datas = $this->setViewData($response['data']);
        return [$datas, $response['count']];
    }

    public function fetchOne(?PigeonCondition $pigeonCondition = null, ?string $order = null): ?array
    {
        list($datas, $count) = $this->fetch($pigeonCondition, 1, 1, $order);
        if (empty($datas)) {
            return null;
        }
        return $datas[0];
    }

    public function fetchAll(?PigeonCondition $pigeonCondition = null, ?string $order = null): array
    {
        $all_datas = [];
        $page = 1;
        $per_page = 100;
        while (true) {
            list($datas, $count) = $this->fetch($pigeonCondition, $page, $per_page, $order);
            $all_datas = array_merge($all_datas, $datas);
            $page++;
            if( $per_page * ($page - 1) > $count || $page > 30 ){
                break;
            }
        }
        return $all_datas;
    }

    public function insert(array $datas): array
    {
        $form_data = $this->toPostData($datas);
        $response = $this->pigeonGateway->postRecord($form_data);
        return $response['data'];
    }

    public function insertOne(array $data): ?int
    {
        $result = $this->insert([$data]);
        if (empty($result[0]['id'])) {
            return null;
        }
        return $result[0]['id'];
    }

    public function update(array $datas): array
    {
        $form_data = $this->toPostData($datas);
        $response = $this->pigeonGateway->postUpdateRecord($form_data);
        return $response['data'];
    }

    public function updateOne(int $id, array $key_vals): bool
    {
        $data = ['id' => $id];
        $data = array_merge($data, $key_vals);
        $result = $this->update([$data]);
        return $result[0]['status']=='success';
    }

    public function delete(int|array $ids): array
    {
        $form_data = $this->getFormDataTemplate();
        if (is_array($ids)) {
            foreach ($ids as $i => $id) {
                $form_data["id[{$i}]"] = $id;
            }
        } else {
            $form_data["id[0]"] = $ids;
        }
        $response = $this->pigeonGateway->postDeleteRecord($form_data);
        return $response['data'];
    }

    public function file(int $file_info_id): string
    {
        $form_data = $this->getFormDataTemplate();
        $form_data['file_info_id'] = $file_info_id;
        return $this->pigeonGateway->getFile($form_data);
    }
}