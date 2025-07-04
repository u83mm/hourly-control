<?php

declare(strict_types=1);

namespace Application\Controller\tasks;

use Application\Core\Controller;
use Application\model\classes\Query;
use Application\model\classes\Validate;
use Application\model\Task;

final class TaskController extends Controller
{
    public function __construct(
        private Query $query = new Query(),
        private Validate $validate = new Validate,    
        private array $fields = [],        
    )
    {
        
    }

    public function index(): void
    {
        try {
            // Test for privileges
            if(!$this->testAccess(['ROLE_ADMIN'])) throw new \Exception('Only admins can access this page');

            $tasks = $this->query->selectAll('tasks');

            $variables = [
                'menus'      => $this->showNavLinks(),
                'session'    => $_SESSION,
                'tasks'      => $tasks,
                'csrf_token' => $this->validate,                
                'active'     => 'administration',
            ];

            $this->render('admin/tasks/index_view.twig', $variables);
            
        } catch (\Throwable $th) {
            if($this->testAccess(['ROLE_ADMIN'])) {
                $error_msg = [
                    "Message:"  =>  $th->getMessage(),
                    "Path:"     =>  $th->getFile(),
                    "Line:"     =>  $th->getLine(),
                ];
            }
            else {
                $error_msg = [
                    'Error:' =>  $th->getMessage(),
                ];
            }

            $this->render('error_view.twig', [
                'menus'             => $this->showNavLinks(),
                'exception_message' => $error_msg,                
            ]);
        }               
    }

    public function new(): void
    {
        try {
            $variables = [
                'menus'      => $this->showNavLinks(),
                'session'    => $_SESSION,
                'csrf_token' => $this->validate,
                'active'     => 'administration',
            ];

            // Test for privileges
            if(!$this->testAccess(['ROLE_ADMIN'])) throw new \Exception('Only admins can access this page');

            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->fields  = [
                    'task_name' => $this->validate->test_input($_POST['task_name']), 
                    'active'    => isset($_POST['task_active']) ? 1 : 0, // Checkbox handling                   
                ];

                if($this->validate->validate_csrf_token() && $this->validate->validate_form($this->fields)) {
                    $this->query->insertInto('tasks', $this->fields);

                    header("Location: /tasks/task/index");
                }
                else {
                    $variables['fields']        = $this->fields;
                    $variables['error_message'] = !$this->validate->validate_form($this->fields) ? 
                                                    $this->validate->get_msg() : 
                                                    "Invalid csrf_token";
                }
            }

            $this->render('admin/tasks/new_task_view.twig', $variables);
            
        } catch (\Throwable $th) {
            if($this->testAccess(['ROLE_ADMIN'])) {
                $error_msg = [
                    "Message:"  =>  $th->getMessage(),
                    "Path:"     =>  $th->getFile(),
                    "Line:"     =>  $th->getLine(),
                ];
            }
            else {
                $error_msg = [
                    'Error:' =>  $th->getMessage(),
                ];
            }

            $this->render('error_view.twig', [
                'menus'             => $this->showNavLinks(),
                'exception_message' => $error_msg,                
            ]);
        }               
    }

    public function edit(): void
    {
        try {
            global $id;

            $variables = [
                'menus'      => $this->showNavLinks(),
                'session'    => $_SESSION,
                'csrf_token' => $this->validate,
                'active'     => 'administration',
            ];

            // Test for privileges
            if(!$this->testAccess(['ROLE_ADMIN'])) throw new \Exception('Only admins can access this page');            

            $task = new Task($this->query->selectOneBy('tasks', 'task_id', $id));
            $this->fields = [
                'task_name' => $task->getTaskName(),
                'active'    => $task->getActive(),
            ];            

            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->fields  = [
                    'task_id'   => $id,
                    'task_name' => $this->validate->test_input($_POST['task_name']),
                    'active'    => isset($_POST['task_active']) ? 1 : 0, // Checkbox handling                    
                ];

                if($this->validate->validate_csrf_token() && $this->validate->validate_form($this->fields)) {
                    $this->query->updateRegistry('tasks', $this->fields, 'task_id');

                    header("Location: /tasks/task/index");
                }
                else {
                    $variables['fields']        = $this->fields;
                    $variables['error_message'] = !$this->validate->validate_form($this->fields) ? 
                                                    $this->validate->get_msg() : 
                                                    "Invalid csrf_token";
                }
            }

            $variables['fields'] = $this->fields;

            $this->render('admin/tasks/edit_task_view.twig', $variables);
            
        } catch (\Throwable $th) {
            if($this->testAccess(['ROLE_ADMIN'])) {
                $error_msg = [
                    "Message:"  =>  $th->getMessage(),
                    "Path:"     =>  $th->getFile(),
                    "Line:"     =>  $th->getLine(),
                ];
            }
            else {
                $error_msg = [
                    'Error:' =>  $th->getMessage(),
                ];
            }

            $this->render('error_view.twig', [
                'menus'             => $this->showNavLinks(),
                'exception_message' => $error_msg,                
            ]);
        }
    }

    public function delete(): void
    {
        try {
            global $id;

            // Test for privileges
            if(!$this->testAccess(['ROLE_ADMIN'])) throw new \Exception('Only admins can access this page');

            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                $_SESSION['csrf_token'] = $_POST['csrf_token'] ?? '';
                
                if($this->validate->validate_csrf_token()) {
                    $this->query->deleteRegistry('tasks', 'task_id', $id);
                    header("Location: /tasks/task/index");
                }
                else {
                    throw new \Exception("Invalid csrf_token");
                }
            }

        } catch (\Throwable $th) {
            if($this->testAccess(['ROLE_ADMIN'])) {
                $error_msg = [
                    "Message:"  =>  $th->getMessage(),
                    "Path:"     =>  $th->getFile(),
                    "Line:"     =>  $th->getLine(),
                ];
            }
            else {
                $error_msg = [
                    'Error:' =>  $th->getMessage(),
                ];
            }

            $this->render('error_view.twig', [
                'menus'             => $this->showNavLinks(),
                'exception_message' => $error_msg,                
            ]);
        }
    }                    
}
