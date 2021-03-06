<?php
namespace CodeDay\Clear\ModelContracts;

use \CodeDay\Clear\Models;

class Venue extends ModelContract
{
    public static function getFields()
    {
        return [
            'name' => [
                'name'          => 'Venue Name',
                'description'   => 'The name of the venue where the event is being held.',
                'example'       => '',
                'value'         => function ($model) { return $model->venue_name; }
            ],

            'address' => [
                'name'          => 'Address',
                'description'   => 'An object containing line_1, line_2, city, state, postal, country.',
                'value'         => function ($model) {
                    return (object)[
                        'line_1' => $model->venue_address_1,
                        'line_2' => $model->venue_address_2,
                        'city' => $model->venue_city,
                        'state' => $model->venue_state,
                        'postal' => $model->venue_postal,
                        'country' => $model->venue_country,
                    ];
                }
            ],

            'contact' => [
                 'name'         => 'Contact',
                 'description'  => 'An object containing venue contact info (name, email, phone)',
                 'requires'     => ['internal'],
                 'value'        => function($model) {
                      return (object)[
                          'first_name' => $model->venue_contact_first_name,
                          'last_name' => $model->venue_contact_last_name,
                          'email' => $model->venue_contact_email,
                          'phone' => $model->venue_contact_phone
                      ];
                 }
            ],

            'full_address' => [
                'name'          => 'Full Address',
                'description'   => 'String containing the full address, for use online.',
                'value'         => function($model) {
                    return $model->venue_full_address;
                }
            ]
        ];
    }
} 
