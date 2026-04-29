<?php

return [
    'referral_stage' => [
        'amicable_settlement' => 'Amicable settlement',
        'reconciliation' => 'Reconciliation',
        'first_instance' => 'First instance',
        'appeal' => 'Appeal',
        'supreme_court' => 'Supreme Court',
        'execution' => 'Execution',
    ],

    'entity' => [
        'case' => 'Case',
        'hearing' => 'Hearing',
        'judgment' => 'Judgment',
        'referral' => 'Referral',
        'document' => 'Document',
        'task' => 'Task',
        'note' => 'Note',
        'assignee' => 'Assignee',
        'timeline' => 'Event',
    ],

    'msg' => [
        'case_created' => [
            'title' => 'Case created',
            'description' => 'Case created: :case_title',
        ],
        'case_updated' => [
            'title' => 'Case updated',
            'description' => 'Case details were updated',
        ],
        'case_status_changed' => [
            'title' => 'Case status changed',
            'description' => 'Status changed from :old to :new',
        ],
        'case_activated' => [
            'title' => 'Case activated',
            'description' => 'The case was activated',
        ],
        'case_deactivated' => [
            'title' => 'Case deactivated',
            'description' => 'The case was deactivated',
        ],
        'referral_created' => [
            'title' => 'Case referral',
            'description' => 'Referral to :stage',
        ],
        'referral_updated' => [
            'title' => 'Referral updated',
            'description' => 'Referral updated — :stage',
        ],
        'referral_deleted' => [
            'title' => 'Referral deleted',
            'description' => 'Referral deleted — :stage',
        ],
        'hearing_created' => [
            'title' => 'New hearing',
            'description' => 'New hearing — :hearing_title',
        ],
        'hearing_updated' => [
            'title' => 'Hearing updated',
            'description' => 'Hearing updated — :hearing_title',
        ],
        'hearing_deleted' => [
            'title' => 'Hearing deleted',
            'description' => 'Hearing deleted — :hearing_title',
        ],
        'judgment_created' => [
            'title' => 'Judgment added',
            'description' => 'Judgment added — :judgment_number',
        ],
        'judgment_updated' => [
            'title' => 'Judgment updated',
            'description' => 'Judgment updated — :judgment_number',
        ],
        'judgment_deleted' => [
            'title' => 'Judgment deleted',
            'description' => 'Judgment deleted — :judgment_number',
        ],
        'document_created' => [
            'title' => 'Document added',
            'description' => 'Document added — :document_name',
        ],
        'document_deleted' => [
            'title' => 'Document deleted',
            'description' => 'Document deleted — :document_name',
        ],
        'task_created' => [
            'title' => 'Task created',
            'description' => 'Task created — :task_title',
        ],
        'task_completed' => [
            'title' => 'Task completed',
            'description' => 'Task completed — :task_title',
        ],
        'task_updated' => [
            'title' => 'Task updated',
            'description' => 'Task updated — :task_title',
        ],
        'task_deleted' => [
            'title' => 'Task deleted',
            'description' => 'Task deleted — :task_title',
        ],
        'assignee_added' => [
            'title' => 'Assignee added',
            'description' => ':user_name was assigned to the case',
        ],
        'assignee_removed' => [
            'title' => 'Assignee removed',
            'description' => ':user_name was removed from the case',
        ],
        'note_created' => [
            'title' => 'Note added',
            'description' => 'Note added — :preview',
        ],
        'note_updated' => [
            'title' => 'Note updated',
            'description' => 'The note was updated',
        ],
    ],
];
