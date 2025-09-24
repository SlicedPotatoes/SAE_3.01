<?php

class abs {
    public $startDate;
    public $endDate;
    public $state;
    public $exam;

    public function __construct($startDate, $endDate, $state, $exam) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->state = $state;
        $this->exam = $exam;
    }
}

$datas = [
    new abs(date_create("2025-09-23"), date_create("2025-09-24"), 'approved', true),
    new abs(date_create("2025-09-28"), date_create("2025-09-30"), 'rejected', false),
    new abs(date_create("2025-10-01"), date_create("2025-10-02"), 'under-review', false),
    new abs(date_create("2025-10-05"), date_create("2025-10-07"), 'unjustified', false),
    new abs(date_create("2025-10-10"), date_create("2025-10-20"), 'pending', false),
];

echo json_encode($datas);