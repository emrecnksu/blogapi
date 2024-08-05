<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Yorum Bildirimi</title>
    <link rel="stylesheet" href="{{ asset('css/email/email.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Yeni Yorum Bildirimi</h1>
        </div>
        <div class="content">
            <h2>Yeni Yorum</h2>
            <p><strong>Yorum:</strong> {{ $comment->content }}</p>
            <p><strong>Yorum Atan Kişi:</strong> {{ $comment->user->name }}</p>
            <p><strong>Post:</strong> {{ $comment->post->title }}</p>
            <p><a href="{{ url('comments/approve/' . $comment->id . '?token=' . $comment->approval_token) }}">Yorumu Onayla</a></p>
        </div>
        <div class="footer">
            <p>Bu e-posta {{ config('app.name') }} tarafından gönderildi.</p>
        </div>
    </div>
</body>
</html>
