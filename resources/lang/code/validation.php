<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => '01', //'The :attribute must be accepted.',
    'active_url' => '02', //'The :attribute is not a valid URL.',
    'after' => '03', //'The :attribute must be a date after :date.',
    'after_or_equal' => '04', //'The :attribute must be a date after or equal to :date.',
    'alpha' => '05', //'The :attribute must only contain letters.',
    'alpha_dash' => '06', //'The :attribute must only contain letters, numbers, dashes and underscores.',
    'alpha_num' => '07', //'The :attribute must only contain letters and numbers.',
    'array' => '08', //'The :attribute must be an array.',
    'before' => '09', //'The :attribute must be a date before :date.',
    'before_or_equal' => '011', //'The :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => '012', //'The :attribute must be between :min and :max.',
        'file' => '013', //'The :attribute must be between :min and :max kilobytes.',
        'string' => '014', //'The :attribute must be between :min and :max characters.',
        'array' => '015', //'The :attribute must have between :min and :max items.',
    ],
    'boolean' => '016', //'The :attribute field must be true or false.',
    'confirmed' => '017', //'The :attribute confirmation does not match.',
    'date' => '018', //'The :attribute is not a valid date.',
    'date_equals' => '019', //'The :attribute must be a date equal to :date.',
    'date_format' => '021', //'The :attribute does not match the format :format.',
    'different' => '022', //'The :attribute and :other must be different.',
    'digits' => '023', //'The :attribute must be :digits digits.',
    'digits_between' => '024', //'The :attribute must be between :min and :max digits.',
    'dimensions' => '025', //'The :attribute has invalid image dimensions.',
    'distinct' => '026', //'The :attribute field has a duplicate value.',
    'email' => '027', //'The :attribute must be a valid email address.',
    'ends_with' => '028', //'The :attribute must end with one of the following: :values.',
    'exists' => '029', //'The selected :attribute is invalid.',
    'file' => '031', //'The :attribute must be a file.',
    'filled' => '032', //'The :attribute field must have a value.',
    'gt' => [
        'numeric' => '033', //'The :attribute must be greater than :value.',
        'file' => '034', //'The :attribute must be greater than :value kilobytes.',
        'string' => '035', //'The :attribute must be greater than :value characters.',
        'array' => '036', //'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => '037', //'The :attribute must be greater than or equal :value.',
        'file' => '038', //'The :attribute must be greater than or equal :value kilobytes.',
        'string' => '039', //'The :attribute must be greater than or equal :value characters.',
        'array' => '041', //'The :attribute must have :value items or more.',
    ],
    'image' => '042', //'The :attribute must be an image.',
    'in' => '043', //'The selected :attribute is invalid.',
    'in_array' => '044', //'The :attribute field does not exist in :other.',
    'integer' => '045', //'The :attribute must be an integer.',
    'ip' => '046', //'The :attribute must be a valid IP address.',
    'ipv4' => '047', //'The :attribute must be a valid IPv4 address.',
    'ipv6' => '048', //'The :attribute must be a valid IPv6 address.',
    'json' => '049', //'The :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => '051', //'The :attribute must be less than :value.',
        'file' => '052', //'The :attribute must be less than :value kilobytes.',
        'string' => '053', //'The :attribute must be less than :value characters.',
        'array' => '054', //'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => '055', //'The :attribute must be less than or equal :value.',
        'file' => '056', //'The :attribute must be less than or equal :value kilobytes.',
        'string' => '057', //'The :attribute must be less than or equal :value characters.',
        'array' => '058', //'The :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => '059', //'The :attribute must not be greater than :max.',
        'file' => '061', //'The :attribute must not be greater than :max kilobytes.',
        'string' => '062', //'The :attribute must not be greater than :max characters.',
        'array' => '063', //'The :attribute must not have more than :max items.',
    ],
    'mimes' => '064', //'The :attribute must be a file of type: :values.',
    'mimetypes' => '065', //'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => '066', //'The :attribute must be at least :min.',
        'file' => '067', //'The :attribute must be at least :min kilobytes.',
        'string' => '068', //'The :attribute must be at least :min characters.',
        'array' => '069', //'The :attribute must have at least :min items.',
    ],
    'multiple_of' => '071', //'The :attribute must be a multiple of :value.',
    'not_in' => '072', //'The selected :attribute is invalid.',
    'not_regex' => '073', //'The :attribute format is invalid.',
    'numeric' => '074', //'The :attribute must be a number.',
    'password' => '075', //'The password is incorrect.',
    'present' => '076', //'The :attribute field must be present.',
    'regex' => '077', //'The :attribute format is invalid.',
    'required' => '078', //'The :attribute field is required.',
    'required_if' => '079', //'The :attribute field is required when :other is :value.',
    'required_unless' => '081', //'The :attribute field is required unless :other is in :values.',
    'required_with' => '082', //'The :attribute field is required when :values is present.',
    'required_with_all' => '083', //'The :attribute field is required when :values are present.',
    'required_without' => '084', //'The :attribute field is required when :values is not present.',
    'required_without_all' => '085', //'The :attribute field is required when none of :values are present.',
    'prohibited_if' => '086', //'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => '087', //'The :attribute field is prohibited unless :other is in :values.',
    'same' => '088', //'The :attribute and :other must match.',
    'size' => [
        'numeric' => '089', //'The :attribute must be :size.',
        'file' => '091', //'The :attribute must be :size kilobytes.',
        'string' => '092', //'The :attribute must be :size characters.',
        'array' => '093', //'The :attribute must contain :size items.',
    ],
    'starts_with' => '094', //'The :attribute must start with one of the following: :values.',
    'string' => '095', //'The :attribute must be a string.',
    'timezone' => '096', //'The :attribute must be a valid zone.',
    'unique' => '097', //'The :attribute has already been taken.',
    'uploaded' => '098', //'The :attribute failed to upload.',
    'url' => '099', //'The :attribute format is invalid.',
    'uuid' => '0111', //'The :attribute must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
//        'post_text' => 'test empty post_text'
    ],

];
