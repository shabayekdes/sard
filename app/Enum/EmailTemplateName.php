<?php

namespace App;

enum EmailTemplateName: string
{
    case NEW_INVOICE = 'New Invoice';
    case INVOICE_SENT = 'Invoice Sent';
    case NEW_JUDGE = 'New Judge';
    case NEW_REGULATORY_BODY = 'New Regulatory Body';
    case NEW_TEAM_MEMBER = 'New Team Member';
    case NEW_CASE = 'New Case';
    case NEW_HEARING = 'New Hearing';
    case NEW_LICENSE = 'New License';
    case NEW_COURT = 'New Court';
    case NEW_TASK = 'New Task';
    case NEW_CLE_RECORD = 'New CLE Record';
    case NEW_CLIENT = 'New Client';
}
