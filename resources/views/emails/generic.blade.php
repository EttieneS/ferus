<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectLine ?? 'Mail' }}</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.5; padding: 1rem;">
    <h2>{{ $subjectLine ?? 'No Subject' }}</h2>
    <p>{!! nl2br(e($bodyText ?? '')) !!}</p>
</body>

</html>