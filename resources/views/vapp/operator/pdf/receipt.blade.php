<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>VAPP Receipt</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 6px; text-align: left; }
        .section { margin-top: 15px; }
    </style>
</head>
<body>

    <h2>VAPPs RECEIPT ACKNOWLEDGEMENT</h2>

    <p><strong>Event:</strong> {{ $eventName }}</p>

    <p>
        I, the undersigned, acknowledge receipt of the Vehicle Access and/or Parking Permit(s) as per the table below 
        and verify that all the permits have been delivered in perfect condition by the Mobility Team.
    </p>

    <p>
        I agree to take possession of the enclosed VAPPs and assume full responsibility for distributing them to their 
        rightful owners. I will ensure that end-users are aware of all rules and regulations governing VAPP use and that 
        the VAPPs are used only in accordance with the guidelines established by the Mobility Team during the event.
    </p>

    <div class="section">
        <strong>VAPP Terms and Conditions:</strong>
        <ul>
            <li>VAPPs must be correctly displayed on the right lower corner of the windshield...</li>
            <li>VAPP is non-transferable.</li>
            <li>The VAPP is valid only for the timing, access, and/or parking zone stated...</li>
            <li>VAPP Holders must follow parking rules and instructions from traffic marshals.</li>
            <li>On-street parking is not permitted...</li>
            <li>Traffic Police may impose penalties or tow vehicles...</li>
            <li>Vehicles not displaying VAPPs will be removed at the user’s expense.</li>
            <li>SC Mobility Team may revoke any VAPP in case of violations.</li>
            <li>For stolen/damaged VAPPs contact vappsys@scqa0.onmicrosoft.com.</li>
        </ul>
    </div>

    <div class="section">
        <strong>VAPP Recipient Information:</strong><br>
        Name: <br>
        QID:  <br>
        Phone: <br>
    </div>

    <div class="section">
        <table>
            <thead>
                <tr>
                    <th>Venue</th>
                    <th>Request Number</th>
                    <th>Match Category</th>
                    <th>Match</th>
                    <th>VAPP Codes Received</th>
                    <th>Number of VAPPs</th>
                    <th>VAPP Serial Numbers</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vapps as $vapp)
                    <tr>
                        <td>{{ $vapp->venue?->short_name }}</td>
                        <td>{{ $vapp->request_number }}</td>
                        <td>{{ $vapp->match_category?->title }}</td>
                        <td>{{ $vapp->match?->match_code }}</td>
                        <td>{{ $vapp->parking?->parking_code }}</td>
                        <td>{{ $vapp->approved_vapps }}</td>
                        <td>{{ $vapp->serial_numbers }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <br><br><br>
    <table style="border:0; width:100%; margin-top:40px;">
        <tr>
            <td style="border:0; text-align:center;">
                _______________________ <br> Signature
            </td>
            <td style="border:0; text-align:center;">
                _______________________ <br> Date
            </td>
        </tr>
    </table>

</body>
</html>
