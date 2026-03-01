<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case UploadReceipt = 'upload_receipt';
    case OverTheCounter = 'over_the_counter';
}
