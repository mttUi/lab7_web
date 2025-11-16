<?php
// Класс для работы с сущностью Student
class Student {
    private $name;
    private $email;
    private $createdAt;
    
    public function __construct($name, $email = '') {
        $this->name = $name;
        $this->email = $email;
        $this->createdAt = date('Y-m-d H:i:s');
    }
    
    public function toArray() {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'timestamp' => $this->createdAt
        ];
    }
    
    public function saveToQueue(QueueManager $queue) {
        $queue->publish($this->toArray());
    }
}