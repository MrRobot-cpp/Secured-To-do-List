
<?php
use PHPUnit\Framework\TestCase;


require_once __DIR__ . '/../../Model/User.php';
require_once __DIR__ . '/../../Model/Database.php';

class UserTest extends TestCase {
    private $db;
    private $user;

    protected function setUp(): void {
        $this->db = Database::getInstance()->getConnection();
        $this->user = new User($this->db);
    }

    public function testEncryptEmail() {
        $email = "test@example.com";
        $encryptedEmail = $this->user->getdecryptEmail($this->user->encryptEmail($email));
        $this->assertEquals($email, $encryptedEmail);
    }

    public function testRegisterUser() {
        $fullName = "Test User";
        $email = "testuser@example.com";
        $password = "password123";
        $usertypes_id = 2;

        $result = $this->user->registerUser($fullName, $email, $password, $usertypes_id);
        $this->assertTrue($result);
    }

    public function testGetUserByEmail() {
        $email = "testuser@example.com";
        $user = $this->user->getUserByEmail($email);
        $this->assertIsArray($user);
        $this->assertEquals($email, $this->user->getdecryptEmail($user['email']));
    }

    public function testVerifyUser() {
        $email = "testuser@example.com";
        $password = "password123";
        $user = $this->user->verifyUser($email, $password);
        $this->assertIsArray($user);
    }

    public function testUsernameExists() {
        $fullName = "Test User";
        $exists = $this->user->usernameExists($fullName);
        $this->assertTrue($exists);
    }

    public function testEmailExists() {
        $email = "testuser@example.com";
        $exists = $this->user->emailExists($email);
        $this->assertTrue($exists);
    }

    public function testGetUserById() {
        $userId = 1;
        $user = $this->user->getUserById($userId);
        $this->assertIsArray($user);
        $this->assertEquals($userId, $user['id']);
    }

    public function testUpdatePassword() {
        $email = "testuser@example.com";
        $newPassword = "newpassword123";
        $result = $this->user->updatePassword($email, $newPassword);
        $this->assertTrue($result);
    }

    public function testGetAllUsers() {
        $users = $this->user->getAllUsers();
        $this->assertIsArray($users);
        $this->assertNotEmpty($users);
    }
}
?>