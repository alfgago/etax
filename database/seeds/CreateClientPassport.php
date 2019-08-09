<?php

use Illuminate\Database\Seeder;

class CreateClientPassport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $oauthClients = [
            [
                'id'            => 1,
                'user_id'       => null,
                'name'          => 'eTax Personal Access Client',
                'secret'        => '2E8w0kkwGcIfrFTz5MSVwlton9Lh2LvML5ukxDvT',
                'redirect'      => 'http://localhost',
                'personal_access_client' => 1,
                'password_client'        => 0,
                'revoked'                => 0,
                'created_at'             => '2019-08-08 22:10:39',
                'updated_at'             => '2019-08-8 22:10:39'
            ],
            [
                'id'            => 2,
                'user_id'       => null,
                'name'          => 'eTax Password Grant Client',
                'secret'        => '3zONf0z0it4UshwrG3EdrCfApVE9sAShEHZejZsE',
                'redirect'      => ' http://localhost',
                'personal_access_client' => 0,
                'password_client'        => 1,
                'revoked'                => 0,
                'created_at'             => '2019-08-08 22:10:39',
                'updated_at'             => '2019-08-8 22:10:39'
            ],
        ];

        foreach ($oauthClients as $oauthClient) {
            $existing = DB::table('oauth_clients')->where('id', $oauthClient['id'])->first();
            if (empty($existing)) {
                DB::table('oauth_clients')->insert($oauthClient);
            } else {
                DB::table('oauth_clients')->where('id', $existing->id)->update($oauthClient);
            }
        }


        $oauthPersonalAccessClients = [
            [
                'id'            => 1,
                'client_id'     => 1,
                'created_at'    => '2018-04-24 22:10:39',
                'updated_at'    => '2018-04-24 22:10:39'
            ]
        ];

        foreach ($oauthPersonalAccessClients as $oauthPersonalAccessClient) {
            $existing = DB::table('oauth_personal_access_clients')
                ->where('id', $oauthPersonalAccessClient['id'])->first();
            if (empty($existing)) {
                DB::table('oauth_personal_access_clients')->insert($oauthPersonalAccessClient);
            } else {
                DB::table('oauth_personal_access_clients')->where('id', $existing->id)
                    ->update($oauthPersonalAccessClient);
            }
        }
    }
}
