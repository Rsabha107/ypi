<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Youth Programme - Approval: {{ $detail['reference_number'] }}</title>
</head>

<body>
    Good day {{ $detail['guardian_name'] }},

    <p>We would like to confirm that your child, {{ $detail['participant_name'] }}, has been accepted into the
        {{ $detail['participant_type'] }} for the Youth Programme.</p>
    <p>You will be added to a WhatsApp group for further information to be shared and logistic plans to be shared.</p>

    <p>Thank you,<br>
        Youth Programme Team</p>
</body>

</html>
