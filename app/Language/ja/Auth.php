<?php

declare(strict_types=1);

/**
 * Wiki 固有の Auth 言語キー（Shield の Auth.php を上書き・拡張）
 * Language/ja/Auth.php に同じキーを置くと日本語化される。
 */
 return [
      'login'               => 'ログイン',
      'email'               => 'メールアドレス',
      'password'            => 'パスワード',
      'passwordConfirm'     => 'パスワード（確認）',
      'username'            => 'ユーザー名',
      'rememberMe'          => 'ログイン状態を保持',
      'register'            => '新規登録',
      'needAccount'         => 'アカウントをお持ちでない方',
      'haveAccount'         => 'すでにアカウントをお持ちの方',
      'forgotPassword'      => 'パスワードを忘れた方',
      'useMagicLink'        => 'ログインリンクを使用',
      'backToLogin'         => 'ログインに戻る',
      'send'                => '送信',
      'confirm'             => '確認',
      'token'               => '認証コード',
      'confirmEmailAddress' => 'メール認証',
      'emailSendCodeTo'     => '{0} に認証コードを送信します。',
      'emailEnterCode'      => '認証コードを入力',
      'emailConfirmCode'    => 'メールに届いた6桁のコードを入力してください。',
      'totpEnterTitle'      => '認証アプリのコードを入力',
      'totpEnterBody'       => 'Google Authenticator などのアプリに表示されている6桁のコードを入力してください。',
      'checkYourEmail'      => 'メールをご確認ください',
      'magicLinkDetails'    => 'ログインリンクを含むメールを送信しました。有効期限は {0} 分です。',
  ];
