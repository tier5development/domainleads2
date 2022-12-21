<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataTransferController extends Controller
{
    public $count_data;
    public $table_name;
    public $terminate_date = '2020-01-01 00:00:00';
    public $limit = 10000;

    public function __construct(Request $request)
    {
        $this->table_name = $request->route()->parameter('table_name');
        $this->count_data = DB::select("SELECT COUNT(*) AS count FROM ".$this->table_name. " WHERE created_at < '".$this->terminate_date."';");
    }


    public function csv_record()
    {
        $backup_table_name = $this->table_name . '_bkp';
        $count = $this->count_data[0]->count;
        $total_loop = ($count / $this->limit) + 2;
        $columns = 'id,
                    file_name,
                    created_at,
                    updated_at,
                    leads_inserted,
                    domains_inserted,
                    query_time,
                    status';

        for ($i = 0; $i < $total_loop; $i++) {
            $insert_sql = "INSERT INTO " . $backup_table_name . " (" . $columns . ")
                            SELECT " . $columns . "
                            FROM " . $this->table_name . "
                            WHERE created_at < '" . $this->terminate_date . "'
                            LIMIT " . $count . ";";

            $delete_sql = "DELETE FROM ". $this->table_name . " WHERE created_at < '" . $this->terminate_date . "' LIMIT " . $count . ";";

            DB::beginTransaction();

            try {
                DB::insert($insert_sql);
                DB::delete(DB::raw($delete_sql));

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return $e;
            }
        }

        return $this->table_name . "Table transfered successfully";
    }

    public function domains_administrative()
    {
        $backup_table_name = $this->table_name . '_bkp';
        $count = $this->count_data[0]->count;
        $total_loop = ($count / $this->limit) + 2;
	//print $total_loop; dd();
	Log::info("Total loop no ".$total_loop." in table".$this->table_name);
        $columns = 'id,
                    administrative_company,
                    administrative_address,
                    administrative_city,
                    administrative_state,
                    administrative_zip,
                    administrative_country,
                    administrative_email,
                    administrative_phone,
                    administrative_fax,
                    domain_name,
                    created_at';

        for ($i = 0; $i < $total_loop; $i++) {
            Log::info("Loop no ".$i." in table".$this->table_name);
            $insert_sql = "INSERT INTO " . $backup_table_name . " (" . $columns . ")
                            SELECT " . $columns . "
                            FROM " . $this->table_name . "
                            WHERE created_at < '" . $this->terminate_date . "'
                            LIMIT " . $this->limit . ";";

            $delete_sql = "DELETE FROM ". $this->table_name . " WHERE created_at < '" . $this->terminate_date . "' LIMIT " . $this->limit . ";";

            DB::beginTransaction();

            try {
                Log::info("transferring start");

                DB::insert($insert_sql);
                DB::delete(DB::raw($delete_sql));

                DB::commit();

                Log::info("transferring end");
            } catch (\Exception $e) {
                DB::rollback();
                Log::info($e->getMessage());
            }
        }

        return $this->table_name . "Table transfered successfully";
    }

    public function domains_billing()
    {
        $backup_table_name = $this->table_name . '_bkp';
        $count = $this->count_data[0]->count;
        $total_loop = ($count / $this->limit) + 2;
        $columns = 'id,
                    billing_name,
                    billing_company,
                    billing_address,
                    billing_city,
                    billing_state,
                    billing_zip,
                    billing_country,
                    billing_email,
                    billing_phone,
                    billing_fax,
                    domain_name,
                    created_at,
                    updated_at';

        for ($i = 0; $i < $total_loop; $i++) {
            $insert_sql = "INSERT INTO " . $backup_table_name . " (" . $columns . ")
                            SELECT " . $columns . "
                            FROM " . $this->table_name . "
                            WHERE created_at < '" . $this->terminate_date . "'
                            LIMIT " . $count . ";";

            $delete_sql = "DELETE FROM ". $this->table_name . " WHERE created_at < '" . $this->terminate_date . "' LIMIT " . $count . ";";

            DB::beginTransaction();

            try {
                DB::insert($insert_sql);
                DB::delete(DB::raw($delete_sql));

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return $e;
            }
        }

        return $this->table_name . "Table transfered successfully";
    }

    public function domains_feedback()
    {
        $backup_table_name = $this->table_name . '_bkp';
        $count = $this->count_data[0]->count;
        $total_loop = ($count / $this->limit) + 2;
        $columns = 'id,
                    domain_name,
                    curl_error,
                    content,
                    checked,
                    created_at,
                    updated_at';

        for ($i = 0; $i < $total_loop; $i++) {
            $insert_sql = "INSERT INTO " . $backup_table_name . " (" . $columns . ")
                            SELECT " . $columns . "
                            FROM " . $this->table_name . "
                            WHERE created_at < '" . $this->terminate_date . "'
                            LIMIT " . $count . ";";

            $delete_sql = "DELETE FROM ". $this->table_name . " WHERE created_at < '" . $this->terminate_date . "' LIMIT " . $count . ";";

            DB::beginTransaction();

            try {
                DB::insert($insert_sql);
                DB::delete(DB::raw($delete_sql));

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return $e;
            }
        }

        return $this->table_name . "Table transfered successfully";
    }

    public function domains_info()
    {
        $backup_table_name = $this->table_name . '_bkp';
        $count = $this->count_data[0]->count;
        $total_loop = ($count / $this->limit) + 2;
        $columns = 'id,
                    query_time,
                    domains_create_date,
                    domains_update_date,
                    expiry_date,
                    domain_registrar_id,
                    domain_registrar_name,
                    domain_registrar_whois,
                    domain_registrar_url,
                    domain_name,
                    created_at,
                    updated_at';

        for ($i = 0; $i < $total_loop; $i++) {
            $insert_sql = "INSERT INTO " . $backup_table_name . " (" . $columns . ")
                            SELECT " . $columns . "
                            FROM " . $this->table_name . "
                            WHERE created_at < '" . $this->terminate_date . "'
                            LIMIT " . $count . ";";

            $delete_sql = "DELETE FROM ". $this->table_name . " WHERE created_at < '" . $this->terminate_date . "' LIMIT " . $count . ";";

            DB::beginTransaction();

            try {
                DB::insert($insert_sql);
                DB::delete(DB::raw($delete_sql));

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return $e;
            }
        }

        return $this->table_name . "Table transfered successfully";
    }

    public function domains_nameserver()
    {
        $backup_table_name = $this->table_name . '_bkp';
        $count = $this->count_data[0]->count;
        $total_loop = ($count / $this->limit) + 2;
        $columns = 'id,
                    name_server_1,
                    name_server_2,
                    name_server_3,
                    name_server_4,
                    domain_name,
                    created_at,
                    updated_at';

        for ($i = 0; $i < $total_loop; $i++) {
            $insert_sql = "INSERT INTO " . $backup_table_name . " (" . $columns . ")
                            SELECT " . $columns . "
                            FROM " . $this->table_name . "
                            WHERE created_at < '" . $this->terminate_date . "'
                            LIMIT " . $count . ";";

            $delete_sql = "DELETE FROM ". $this->table_name . " WHERE created_at < '" . $this->terminate_date . "' LIMIT " . $count . ";";

            DB::beginTransaction();

            try {
                DB::insert($insert_sql);
                DB::delete(DB::raw($delete_sql));

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return $e;
            }
        }

        return $this->table_name . "Table transfered successfully";
    }

    public function domains_status()
    {
        $backup_table_name = $this->table_name . '_bkp';
        $count = $this->count_data[0]->count;
        $total_loop = ($count / $this->limit) + 2;
        $columns = 'id,
                    domain_status_1,
                    domain_status_2,
                    domain_status_3,
                    domain_status_4,
                    domain_name,
                    created_at,
                    updated_at';

        for ($i = 0; $i < $total_loop; $i++) {
            $insert_sql = "INSERT INTO " . $backup_table_name . " (" . $columns . ")
                            SELECT " . $columns . "
                            FROM " . $this->table_name . "
                            WHERE created_at < '" . $this->terminate_date . "'
                            LIMIT " . $count . ";";

            $delete_sql = "DELETE FROM ". $this->table_name . " WHERE created_at < '" . $this->terminate_date . "' LIMIT " . $count . ";";

            DB::beginTransaction();

            try {
                DB::insert($insert_sql);
                DB::delete(DB::raw($delete_sql));

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return $e;
            }
        }

        return $this->table_name . "Table transfered successfully";
    }

    public function domains_technical()
    {
        $backup_table_name = $this->table_name . '_bkp';
        $count = $this->count_data[0]->count;
        $total_loop = ($count / $this->limit) + 2;
        $columns = 'id,
                    technical_name,
                    technical_company,
                    technical_address,
                    technical_city,
                    technical_state,
                    technical_zip,
                    technical_country,
                    technical_email,
                    technical_phone,
                    technical_fax,
                    domain_name,
                    created_at,
                    updated_at';

        for ($i = 0; $i < $total_loop; $i++) {
            $insert_sql = "INSERT INTO " . $backup_table_name . " (" . $columns . ")
                            SELECT " . $columns . "
                            FROM " . $this->table_name . "
                            WHERE created_at < '" . $this->terminate_date . "'
                            LIMIT " . $count . ";";

            $delete_sql = "DELETE FROM ". $this->table_name . " WHERE created_at < '" . $this->terminate_date . "' LIMIT " . $count . ";";

            DB::beginTransaction();

            try {
                DB::insert($insert_sql);
                DB::delete(DB::raw($delete_sql));

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return $e;
            }
        }

        return $this->table_name . "Table transfered successfully";
    }

    public function download_csv()  // *
    {
        $backup_table_name = $this->table_name . '_bkp';
        $count = $this->count_data[0]->count;
        $total_loop = ($count / $this->limit) + 2;
        $columns = 'id,
                    user_id,
                    download_data,
                    status,
                    created_at,
                    updated_at';

        for ($i = 0; $i < $total_loop; $i++) {
            $insert_sql = "INSERT INTO " . $backup_table_name . " (" . $columns . ")
                            SELECT " . $columns . "
                            FROM " . $this->table_name . "
                            WHERE created_at < '" . $this->terminate_date . "'
                            LIMIT " . $count . ";";

            $delete_sql = "DELETE FROM ". $this->table_name . " WHERE created_at < '" . $this->terminate_date . "' LIMIT " . $count . ";";

            DB::beginTransaction();

            try {
                DB::insert($insert_sql);
                DB::delete(DB::raw($delete_sql));

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return $e;
            }
        }

        return $this->table_name . "Table transfered successfully";
    }

    public function leads()
    {
        $backup_table_name = $this->table_name . '_bkp';
        $count = $this->count_data[0]->count;
        $total_loop = ($count / $this->limit) + 2;
        $columns = 'id,
                    registrant_fname,  
                    registrant_lname,
                    registrant_email,
                    registrant_company,
                    registrant_address,
                    registrant_city,
                    registrant_state,
                    registrant_zip,
                    registrant_country,
                    registrant_phone,
                    phone_validated,
                    unlocked_num,
                    |omains_count,
                    registrant_fax,
                    created_at,
                    updated_at';

        for ($i = 0; $i < $total_loop; $i++) {
            $insert_sql = "INSERT INTO " . $backup_table_name . " (" . $columns . ")
                            SELECT " . $columns . "
                            FROM " . $this->table_name . "
                            WHERE created_at < '" . $this->terminate_date . "'
                            LIMIT " . $count . ";";

            $delete_sql = "DELETE FROM ". $this->table_name . " WHERE created_at < '" . $this->terminate_date . "' LIMIT " . $count . ";";

            DB::beginTransaction();

            try {
                DB::insert($insert_sql);
                DB::delete(DB::raw($delete_sql));

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return $e;
            }
        }

        return $this->table_name . "Table transfered successfully";
    }

    public function leadusers()  // *
    {
        $backup_table_name = $this->table_name . '_bkp';
        $count = $this->count_data[0]->count;
        $total_loop = ($count / $this->limit) + 2;
        $columns = 'id,
                    user_id,
                    registrant_email,
                    domain_name,
                    registrant_country,
                    registrant_fname,
                    registrant_lname,
                    registrant_company,
                    registrant_phone,
                    number_type,
                    domains_create_date,
                    expiry_date,
                    deleted_at,
                    created_at,
                    updated_at ';

        for ($i = 0; $i < $total_loop; $i++) {
            $insert_sql = "INSERT INTO " . $backup_table_name . " (" . $columns . ")
                            SELECT " . $columns . "
                            FROM " . $this->table_name . "
                            WHERE created_at < '" . $this->terminate_date . "'
                            LIMIT " . $count . ";";

            $delete_sql = "DELETE FROM ". $this->table_name . " WHERE created_at < '" . $this->terminate_date . "' LIMIT " . $count . ";";

            DB::beginTransaction();

            try {
                DB::insert($insert_sql);
                DB::delete(DB::raw($delete_sql));

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return $e;
            }
        }

        return $this->table_name . "Table transfered successfully";
    }

    public function search_metadata()
    {
        $backup_table_name = $this->table_name . '_bkp';
        $count = $this->count_data[0]->count;
        $total_loop = ($count / $this->limit) + 2;
        $columns = 'id,
                    domain_name,
                    domain_ext,
                    registrant_country,
                    registrant_state,
                    domains_create_date1,
                    domains_create_date2,
                    domains_count,
                    number_type,
                    sortby,
                    domains_count_operator,
                    leads_unlocked_operator,
                    unlocked_num,
                    search_priority,
                    totalLeads,
                    totalDomains,
                    compression_level,
                    created_at,
                    updated_at,
                    leads,
                    registrant_zip,
                    query_time,
                    expiry_date,
                    expiry_date2';

        for ($i = 0; $i < $total_loop; $i++) {
            $insert_sql = "INSERT INTO " . $backup_table_name . " (" . $columns . ")
                            SELECT " . $columns . "
                            FROM " . $this->table_name . "
                            WHERE created_at < '" . $this->terminate_date . "'
                            LIMIT " . $count . ";";

            $delete_sql = "DELETE FROM ". $this->table_name . " WHERE created_at < '" . $this->terminate_date . "' LIMIT " . $count . ";";

            DB::beginTransaction();

            try {
                DB::insert($insert_sql);
                DB::delete(DB::raw($delete_sql));

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return $e;
            }
        }

        return $this->table_name . "Table transfered successfully";
    }

}
