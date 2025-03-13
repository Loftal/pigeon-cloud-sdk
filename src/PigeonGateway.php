<?php

namespace PigeonCloudSdk;

class PigeonGateway
{
    private string $auth;
    private bool $debug;
    public function __construct(string $auth, bool $debug = false)
    {
        $this->auth = $auth;
        $this->debug = $debug;
    }

    public function setDebug(bool $debug = true): void
    {
        $this->debug = $debug;
    }

    /*
     * @param array{
     *   table: string
     * } $form_data
     */
    public function getFields(array $form_data): array
    {
        return PigeonHttpRequest::get('/fields', $form_data, $this->auth, $this->debug);
    }

    /*
     * @param array{
     *   table: string
     *   search: array
     *   limit: int
     *   offset: int
     * } $form_data
     */
    public function getRecord(array $form_data): array
    {
        return PigeonHttpRequest::get('/record', $form_data, $this->auth, $this->debug);
    }

    /*
     * @param array{
     *   table: string
     *   search: array
     *   limit: int
     *   offset: int
     * } $form_data
     */
    public function postGetRecord(array $form_data): array
    {
        return PigeonHttpRequest::post('/get_record', $form_data, $this->auth, $this->debug);
    }

    /*
     * @param array{
     *   table: string
     *   data: array<array{"id": ?int, "field__X": int|string}>
     * } $form_data
     */
    public function postRecord(array $form_data): array
    {
        return PigeonHttpRequest::post('/record', $form_data, $this->auth, $this->debug);
    }

    /*
     * @param array{
     *   table: string
     *   data: array<array{"id": int, "field__X": int|string}>
     * } $form_data
     */
    public function postUpdateRecord($form_data): array
    {
        return PigeonHttpRequest::post('/update_record', $form_data, $this->auth, $this->debug);
    }

    public function postDeleteRecord($form_data): array
    {
        return PigeonHttpRequest::post('/delete_record', $form_data, $this->auth, $this->debug);
    }

    public function getFile($form_data): string
    {
        return PigeonHttpRequest::get('/file', $form_data, $this->auth, $this->debug);
    }
}