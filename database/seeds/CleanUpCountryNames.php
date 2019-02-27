<?php

use Illuminate\Database\Seeder;

class CleanUpCountryNames extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
        * UPDATE leads
        * SET `registrant_country` = 
        * CASE 
        * WHEN LOWER(`registrant_country`) LIKE 'united states of america' THEN REPLACE(LOWER(`registrant_country`), 'united states of america', 'United States')
        * WHEN LOWER(`registrant_country`) LIKE 'china union' THEN REPLACE(LOWER(`registrant_country`), 'china union', 'China')
        * END
        * WHERE LOWER(`registrant_country`) LIKE 'united states of america' OR LOWER(`registrant_country`) LIKE 'china union';
        */

        $impureCountryNames = custom_country_aliases();
        /**
         * key => impure country name
         * val => pure country name
         */

        $queryStart =   "UPDATE leads SET registrant_country = CASE ";
        $queryEnd   =   '';
        $query      =   '';
        foreach ($impureCountryNames as $impureCountry => $pureCountry) {
            $query .= "WHEN LOWER(registrant_country) LIKE '".$impureCountry."' THEN REPLACE(LOWER(registrant_country), '".$impureCountry."', '".$pureCountry."') ";
            $queryEnd .= strlen($queryEnd) == 0 
                ?   " WHERE LOWER(registrant_country) LIKE '".$impureCountry."' "
                :   " OR LOWER(registrant_country) LIKE '".$impureCountry."' ";
        }
        $queryEnd = ' END '.$queryEnd;
        $finalQuery = $queryStart.$query.$queryEnd;
        DB::statement($finalQuery);
    }
}
