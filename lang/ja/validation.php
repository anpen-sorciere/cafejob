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

    'accepted' => ':attributeを承認してください。',
    'accepted_if' => ':otherが:valueの場合、:attributeを承認してください。',
    'active_url' => ':attributeは有効なURLではありません。',
    'after' => ':attributeは:dateより後の日付である必要があります。',
    'after_or_equal' => ':attributeは:date以降の日付である必要があります。',
    'alpha' => ':attributeは文字のみを含む必要があります。',
    'alpha_dash' => ':attributeは文字、数字、ダッシュ、アンダースコアのみを含む必要があります。',
    'alpha_num' => ':attributeは文字と数字のみを含む必要があります。',
    'array' => ':attributeは配列である必要があります。',
    'ascii' => ':attributeは半角英数字と記号のみを含む必要があります。',
    'before' => ':attributeは:dateより前の日付である必要があります。',
    'before_or_equal' => ':attributeは:date以前の日付である必要があります。',
    'between' => [
        'array' => ':attributeは:min個から:max個の項目を含む必要があります。',
        'file' => ':attributeは:min KBから:max KBの間である必要があります。',
        'numeric' => ':attributeは:minから:maxの間である必要があります。',
        'string' => ':attributeは:min文字から:max文字の間である必要があります。',
    ],
    'boolean' => ':attributeフィールドはtrueまたはfalseである必要があります。',
    'confirmed' => ':attributeの確認が一致しません。',
    'current_password' => 'パスワードが正しくありません。',
    'date' => ':attributeは有効な日付ではありません。',
    'date_equals' => ':attributeは:dateと同じ日付である必要があります。',
    'date_format' => ':attributeは:format形式と一致しません。',
    'decimal' => ':attributeは:decimal桁の小数である必要があります。',
    'declined' => ':attributeを拒否してください。',
    'declined_if' => ':otherが:valueの場合、:attributeを拒否してください。',
    'different' => ':attributeと:otherは異なる必要があります。',
    'digits' => ':attributeは:digits桁である必要があります。',
    'digits_between' => ':attributeは:min桁から:max桁の間である必要があります。',
    'dimensions' => ':attributeの画像サイズが無効です。',
    'distinct' => ':attributeフィールドに重複した値があります。',
    'doesnt_end_with' => ':attributeは次のいずれかで終わってはいけません: :values。',
    'doesnt_start_with' => ':attributeは次のいずれかで始まってはいけません: :values。',
    'email' => ':attributeは有効なメールアドレスである必要があります。',
    'ends_with' => ':attributeは次のいずれかで終わる必要があります: :values。',
    'enum' => '選択された:attributeは無効です。',
    'exists' => '選択された:attributeは無効です。',
    'file' => ':attributeはファイルである必要があります。',
    'filled' => ':attributeフィールドには値が必要です。',
    'gt' => [
        'array' => ':attributeは:value個より多い項目を含む必要があります。',
        'file' => ':attributeは:value KBより大きい必要があります。',
        'numeric' => ':attributeは:valueより大きい必要があります。',
        'string' => ':attributeは:value文字より長い必要があります。',
    ],
    'gte' => [
        'array' => ':attributeは:value個以上の項目を含む必要があります。',
        'file' => ':attributeは:value KB以上である必要があります。',
        'numeric' => ':attributeは:value以上である必要があります。',
        'string' => ':attributeは:value文字以上である必要があります。',
    ],
    'image' => ':attributeは画像である必要があります。',
    'in' => '選択された:attributeは無効です。',
    'in_array' => ':attributeフィールドは:otherに存在しません。',
    'integer' => ':attributeは整数である必要があります。',
    'ip' => ':attributeは有効なIPアドレスである必要があります。',
    'ipv4' => ':attributeは有効なIPv4アドレスである必要があります。',
    'ipv6' => ':attributeは有効なIPv6アドレスである必要があります。',
    'json' => ':attributeは有効なJSON文字列である必要があります。',
    'lowercase' => ':attributeは小文字である必要があります。',
    'lt' => [
        'array' => ':attributeは:value個より少ない項目を含む必要があります。',
        'file' => ':attributeは:value KBより小さい必要があります。',
        'numeric' => ':attributeは:valueより小さい必要があります。',
        'string' => ':attributeは:value文字より短い必要があります。',
    ],
    'lte' => [
        'array' => ':attributeは:value個以下の項目を含む必要があります。',
        'file' => ':attributeは:value KB以下である必要があります。',
        'numeric' => ':attributeは:value以下である必要があります。',
        'string' => ':attributeは:value文字以下である必要があります。',
    ],
    'mac_address' => ':attributeは有効なMACアドレスである必要があります。',
    'max' => [
        'array' => ':attributeは:max個以下の項目を含む必要があります。',
        'file' => ':attributeは:max KB以下である必要があります。',
        'numeric' => ':attributeは:max以下である必要があります。',
        'string' => ':attributeは:max文字以下である必要があります。',
    ],
    'max_digits' => ':attributeは:max桁以下である必要があります。',
    'mimes' => ':attributeは次のタイプのファイルである必要があります: :values。',
    'mimetypes' => ':attributeは次のタイプのファイルである必要があります: :values。',
    'min' => [
        'array' => ':attributeは少なくとも:min個の項目を含む必要があります。',
        'file' => ':attributeは少なくとも:min KBである必要があります。',
        'numeric' => ':attributeは少なくとも:minである必要があります。',
        'string' => ':attributeは少なくとも:min文字である必要があります。',
    ],
    'min_digits' => ':attributeは少なくとも:min桁である必要があります。',
    'missing' => ':attributeフィールドは存在してはいけません。',
    'missing_if' => ':otherが:valueの場合、:attributeフィールドは存在してはいけません。',
    'missing_unless' => ':otherが:valueでない限り、:attributeフィールドは存在してはいけません。',
    'missing_with' => ':valuesが存在する場合、:attributeフィールドは存在してはいけません。',
    'missing_with_all' => ':valuesが存在する場合、:attributeフィールドは存在してはいけません。',
    'multiple_of' => ':attributeは:valueの倍数である必要があります。',
    'not_in' => '選択された:attributeは無効です。',
    'not_regex' => ':attributeの形式が無効です。',
    'numeric' => ':attributeは数値である必要があります。',
    'password' => [
        'letters' => ':attributeには少なくとも1文字の文字を含める必要があります。',
        'mixed' => ':attributeには少なくとも1文字の大文字と1文字の小文字を含める必要があります。',
        'numbers' => ':attributeには少なくとも1つの数字を含める必要があります。',
        'symbols' => ':attributeには少なくとも1つの記号を含める必要があります。',
        'uncompromised' => '指定された:attributeがデータ漏洩で見つかりました。別の:attributeを選択してください。',
    ],
    'present' => ':attributeフィールドが存在する必要があります。',
    'present_if' => ':otherが:valueの場合、:attributeフィールドが存在する必要があります。',
    'present_unless' => ':otherが:valueでない限り、:attributeフィールドが存在する必要があります。',
    'present_with' => ':valuesが存在する場合、:attributeフィールドが存在する必要があります。',
    'present_with_all' => ':valuesが存在する場合、:attributeフィールドが存在する必要があります。',
    'prohibited' => ':attributeフィールドは禁止されています。',
    'prohibited_if' => ':otherが:valueの場合、:attributeフィールドは禁止されています。',
    'prohibited_unless' => ':otherが:valueでない限り、:attributeフィールドは禁止されています。',
    'prohibits' => ':attributeフィールドは:otherが存在することを禁止しています。',
    'regex' => ':attributeの形式が無効です。',
    'required' => ':attributeフィールドは必須です。',
    'required_array_keys' => ':attributeフィールドには次のエントリが含まれている必要があります: :values。',
    'required_if' => ':otherが:valueの場合、:attributeフィールドは必須です。',
    'required_if_accepted' => ':otherが承認された場合、:attributeフィールドは必須です。',
    'required_unless' => ':otherが:valuesでない限り、:attributeフィールドは必須です。',
    'required_with' => ':valuesが存在する場合、:attributeフィールドは必須です。',
    'required_with_all' => ':valuesが存在する場合、:attributeフィールドは必須です。',
    'required_without' => ':valuesが存在しない場合、:attributeフィールドは必須です。',
    'required_without_all' => ':valuesが存在しない場合、:attributeフィールドは必須です。',
    'same' => ':attributeと:otherは一致する必要があります。',
    'size' => [
        'array' => ':attributeには:size個の項目を含める必要があります。',
        'file' => ':attributeは:size KBである必要があります。',
        'numeric' => ':attributeは:sizeである必要があります。',
        'string' => ':attributeは:size文字である必要があります。',
    ],
    'starts_with' => ':attributeは次のいずれかで始まる必要があります: :values。',
    'string' => ':attributeは文字列である必要があります。',
    'timezone' => ':attributeは有効なタイムゾーンである必要があります。',
    'unique' => ':attributeは既に使用されています。',
    'uploaded' => ':attributeのアップロードに失敗しました。',
    'uppercase' => ':attributeは大文字である必要があります。',
    'url' => ':attributeは有効なURLである必要があります。',
    'ulid' => ':attributeは有効なULIDである必要があります。',
    'uuid' => ':attributeは有効なUUIDである必要があります。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "rule.attribute" to name the lines. This makes it quick to
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
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'password_confirmation' => 'パスワード（確認）',
        'username' => 'ユーザー名',
        'first_name' => '名',
        'last_name' => '姓',
        'birth_date' => '生年月日',
        'title' => 'タイトル',
        'content' => '内容',
        'rating' => '評価',
        'name' => '名前',
        'shop_id' => '店舗',
        'description' => '説明',
        'address' => '住所',
        'phone' => '電話番号',
        'website' => 'ウェブサイト',
        'opening_hours' => '営業時間',
        'concept_type' => 'コンセプトタイプ',
        'uniform_type' => '制服タイプ',
        'job_type' => '雇用形態',
        'salary_min' => '最低給与',
        'salary_max' => '最高給与',
        'work_hours' => '勤務時間',
        'requirements' => '応募条件',
        'benefits' => '福利厚生',
        'gender_requirement' => '性別',
        'age_min' => '最低年齢',
        'age_max' => '最高年齢',
        'status' => 'ステータス',
        'application_deadline' => '応募締切',
    ],

];

