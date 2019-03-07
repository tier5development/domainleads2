<?php

use Illuminate\Database\Seeder;
use App\Review;
class createReviewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $arr = [
            [
                'name' => 'Marie Gardiner',
                'img' => config('settings.APPLICATION-DOMAIN').'/public/images/'.'marie_gardiner.jpg',
                'review' => 'Easy to use program that generates leads for my business. 
                    Perfect for anyone doing B2B sales and service for sure! The bonus of this program - it just keeps getting better and better! Updates, redesigns, features, 
                    it really is one of my fav tools!'
            ],[
                'name' => 'Mark Harvey',
                'img' => config('settings.APPLICATION-DOMAIN').'/public/images/'.'mark_harvey.jpg',
                'review' => 'Domain Leads has been an easy process to use to get very fast results. I highly recommend this for everybody.'
            ],[
                'name' => 'Johnny Baxter',
                'img' => config('settings.APPLICATION-DOMAIN').'/public/images/'.'johnny_baxter.jpg',
                'review' => 'Domain Leads gives you the ability to offer your services such as website development, seo, marketing, etc to new domains by contacting them right after a new domain is registered. I highly recommend DL.'
            ],[
                'name' => 'Niel Reichl',
                'img' => config('settings.APPLICATION-DOMAIN').'/public/images/'.'niel_reichl.jpg',
                'review' => 'Domain leads is the secret weapon every smart entrepreneur must invest in.
                    Unless you\'re a big fan of the feast and famine and hope that a client knocks on your door.
                    Thanks for building this Tier5!!!'
            ],[
                'name' => 'LaTricia Hammer',
                'img' => config('settings.APPLICATION-DOMAIN').'/public/images/'.'latricia_hammer.jpg',
                'review' => 'Domain Leads is one of The Best ways to reach out to prospects in the B2B space! Simple to use and implement.'
            ],[
                'name' => 'Homayun Alizay',
                'img' => config('settings.APPLICATION-DOMAIN').'/public/images/'.'homayun_alizay.jpg',
                'review' => 'Great software! As an Internet agency with services like domain registration and parking domains international I am glad that I have used this software.
                    From the start I got leads, thanks. In case you are going to use this great software to notice when a domain expires, feel free to holla at me or the makers of this lead generation platform to get help with registration or parking your domains.
                    Great job!'
            ],[
                'name' => 'Kevin P DiCarlo',
                'img' => config('settings.APPLICATION-DOMAIN').'/public/images/'.'kevin_p_dicarlo.jpg',
                'review' => 'If your here lookong for leads to grow your business then you came to the right place. 50 leads a day equals 3k a month. How can you not grab that offer? Especially at the price. I would grab now before price goes up!'
            ],[
                'name' => 'Hector Santiesteban',
                'img' => config('settings.APPLICATION-DOMAIN').'/public/images/'.'hector_santiesteban.jpg',
                'review' => 'If you want leads in the e-commerce space-- this is the best tool for you!'
            ],[
                'name' => 'Craig Robert Morrison',
                'img' => config('settings.APPLICATION-DOMAIN').'/public/images/'.'craig_robert_morrison.jpg',
                'review' => 'Domain Leads is a powerful tool for list building in connection with digital marketing. DL queries the internet for new domains released and registered. Searches can be customized for date ranges and keyword searches. Effective for use of timing delivery of your message or ad content in conjunction with a probable web site launch. The time savings over a manual process is immeasurable.'
            ],[
                'name' => 'Mohamed Aalfoulany',
                'img' => config('settings.APPLICATION-DOMAIN').'/public/images/'.'mohamed_aalfoulany.jpg',
                'review' => 'If you are looking for new leads in your niche ,Domain leads is the software you need for sure !!!!!!!'
            ],[
                'name' => 'Orim Ardud',
                'img' => config('settings.APPLICATION-DOMAIN').'/public/images/'.'orim_ardud.jpg',
                'review' => 'Excellent and simple lead gen software for anyone serving any niche! Thinking about getting it? Don\'t. Go and get it.'
            ],[
                'name' => 'Digitalnomadkeenan Baker',
                'img' => config('settings.APPLICATION-DOMAIN').'/public/images/'.'digitalnomadkeenan_baker.jpg',
                'review' => 'Domain Leads is simple to use and you get fast results. I use it for all my websites.'
            ],[
                'name' => 'Naveen Dhana Lak',
                'img' => config('settings.APPLICATION-DOMAIN').'/public/images/'.'naveen_dhana_lak.jpg',
                'review' => 'Awesome Software to get leads and has some cool filter options too !! love using Domain Leads.'
            ],
        ];
        Review::truncate();
        Review::insert($arr);
    }
}
