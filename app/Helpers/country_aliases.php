<?php

/**
 * This function resolves all the error prone impure country name from db to pure country names
 */
function custom_country_aliases() {
    return [
        'united states of america' => 'United States',
        'u.s.a' => 'United States',
        'u.s.a.' => 'United States',
        'usa' => 'United States',
        'great britain' => 'United Kingdom',
        'great britain (uk)' => 'United Kingdom',
        'uk' => 'United Kingdom',
        'de' => 'Germany',
        'de (germany)' => 'Germany',
        'germany (de)' => 'Germany',
        'australia (au)' => 'Australia',
        '(au) australia' => 'Australia',
        'aust' => 'Australia',
        'fr (france)' => 'France',
        'france m?tropolitaine' => 'France',
        'france mtropolitaine' => 'France',
        'ae (united arab emirates)' => 'United Arab Emirates',
        'united arab emirates (ae)' => 'United Arab Emirates',
        'algerie' => 'Algeria',
        'bosnia and herzegowina' => 'Bosnia and Herzegovina',
        'boston, ma 02110' => 'United States',
        'ca (canada)' => 'Canada',
        '(canada) ca' => 'Canada',
        'brasil' => 'Brazil',
        'bulgar' => 'Bulgaria',
        'ch (switzerland)' => 'Switzerland',
        '(switzerland) ch' => 'Switzerland',
        'cn (china)' => 'China',
        'china (cn)' => 'China',
    ];
}

?>