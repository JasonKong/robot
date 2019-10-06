<?php
  class Robot 
  {
    // Command list
    CONST COMMANDS = [
      'PLACE', 'MOVE', 'LEFT', 'RIGHT', 'REPORT', 'EXIT'
    ];

    // direction list for robot
    CONST DIRECTIONS = [
      'EAST', 'SOUTH', 'WEST','NORTH'
    ];

    // arrow icons for display the current direction of robot
    CONST ROBOT_ICONS = [
      'EAST' => "\u{2192}", 
      'SOUTH' => "\u{2193}",
      'WEST' => "\u{2190}",
      'NORTH' => "\u{2191}" 
    ];

    // Defualt table size
    CONST TABLE_WIDTH = 5;
    CONST TABLE_HEIGHT = 5;

    CONST LINE_BREAK = "\n";

    private $_current_position;

    // The flag to indicate the game is started
    private $_start_flag;

    // The flag to indicate to quit from the game
    private $_stop_flag;

    function __construct($file = false) {

      // init default settings
      $this->init();

      if ($file) {
        // read commands from file
        $this->initFile($file);
      } else {
        // Get commands from console
        $this->initConsole();
      }
    }

    /*
     * init the default settings
     */
    function init() {
      $this->_start_flg = false;
      $this->_end_flg = false;
      $this->_current_position = [
        'x' => 0,
        'y' => 0,
        'f' => self::DIRECTIONS[3]
      ];
    }

    /*
     * Init and read command from file 
     */
    function initFile($file_path) {
      if (!$this->checkFile($file_path)) return false;

      $file = fopen($file_path,"r");

      while(! feof($file)) {
         $command = fgets($file);
         if (empty(trim($command))){
           continue;
         }
         $this->checkCommand($command);
      }

      fclose($file);
    }

    /*
     * Check if input file does exist 
     */
    function checkFile($file_path) {
      if (!file_exists($file_path)) {
          $this->showMessage($file_path.' doesn not exist.');
          return false;
      }
      return true;
    }

    /*
     * Init and read command from the console 
     */
    function initConsole() {
      $this->showMessage('Please enter the command for the robot:');
      $this->showMessage('PLACE 1,1,NORTH (Use this command to set the postion of the robot and start the game.)');
      $this->showMessage('MOVE (move the robot one unit forward in the direction it is currently facing )');
      $this->showMessage('LEFT (rotate the robot 90 degrees in the specified direction without changing the position of the robot)');
      $this->showMessage('RIGHT (same as above)');
      $this->showMessage('REPORT (show the current position)');
      $this->showMessage('EXIT (quit from the game)');

      // keep monitoring the user input commands
      do {
        if (PHP_OS == 'WINNT') {
          echo '$ ';
          $line = stream_get_line(STDIN, 1024, PHP_EOL);
        } else {
          $line = readline('$ ');
        }

        // check and excute the command
        $result = $this->checkCommand($line);
      } while (!$this->$_stop_flag); // Exit loop when stop flag is true
      
    }

    function checkCommand($command) {
      // formmat user input command
      $command = trim(strtoupper($command));

      // check if the command is empty
      if (empty($command)) {
        $this->showMessage('Command cannot be empty. Enter EXIT to quit application');
        return false;
      }

      // explode commands into array by space
      $_commands = explode(' ', $command); //todo

      // check if command is available
      if (!in_array($_commands[0], self::COMMANDS)) {
        $this->showMessage('Invalid command');
        return false;
      }

      // if the user enter EXIT, then quit the game
      if ($_commands[0] === 'EXIT') {
        $this->showMessage('Bye!');
        $this->$_stop_flag = true;
        return false;
      }

      // parse and excute the command
      if ($_commands[0] === self::COMMANDS[0]) {
        if(isset($_commands[1])) {
          if (!$this->checkPlaceCommand($_commands[1])) {
            return false;
          }
        } else {
          $this->showMessage('Invalid parameters for '.self::COMMANDS[0]);
          $this->showMessage('Sample: PLACE 1,1,NORTH');
          return false;
        }
      } else {
        // check if the game is started or not
        // if not, ask user to use the PLACE command to start
        if (!$this->_start_flag) {
          $this->showMessage('Please use PLACE command to start the game!');
          return false;
        }

        switch ($_commands[0]) {
          case self::COMMANDS[1]: // MOVE
            $this->move();
            break;
          case self::COMMANDS[2]: // LEFT
          case self::COMMANDS[3]: // RIGHT
            $this->rotate($_commands[0]);
            break;
          case self::COMMANDS[4]: // REPORT
            $this->report();
            break;
          default:
            return false;
        }
      }
    }

    /*
     * check the parameter for PLACE command 
     */
    function checkPlaceCommand($parameter) {
      
      $_params = explode(',', trim($parameter));
      if (is_array($_params) && (count($_params) > 2)) {
        $_x = trim($_params[0]);
        if (!$this->checkPositionParameter($_x, 1)) {
          return false;
        }
        $_y = trim($_params[1]);
        if (!$this->checkPositionParameter($_y, 2)) {
          return false;
        }

        $_f = trim($_params[2]);
        if (!$this->checkDirectionParameter($_f)) {
          return false;
        }

        $this->resetPosition($_x, $_y, $_f);

      } else {
        $this->showMessage('Invalid parameters for '.self::COMMANDS[0]);
        $this->showMessage('Sample: PLACE 1,1,NORTH');
        return false;
      }
    }

    /*
     * check the first and second parameter for PLACE command 
     */
    function checkPositionParameter($val, $number) {
      
      $_max = 0;
      $_str = '';
      if ($number === 1) {
        $_max = self::TABLE_WIDTH;
        $_str = 'first';
      } else if($number === 2) {
        $_max = self::TABLE_HEIGHT;
        $_str = 'second';
      }

      if (is_numeric($val) && intval($val) >= 0 && intval($val) < $_max) {
        return true;
      } else {
        $this->showMessage('The '.$_str.' parameter for PLACE command is invalid. Please enter between 0 to '.($_max - 1).'.');
        return false;
      }
    }

    /*
     * check the third parameter for PLACE command 
     */
    function checkDirectionParameter($val) {
      if (in_array(strtoupper($val), self::DIRECTIONS)) {
        return true;
      } else {
        $this->showMessage('The thrid parameter for PLACE command is invalid. Please use '.(json_encode(self::DIRECTIONS)));
        return false;
      }
    }

    /*
     * reset the position
     */
    function resetPosition($x, $y, $direction) {
      $this->_current_position = [
          'x' => intval($x), 
          'y' => intval($y), 
          'f' => $direction
        ];

      $this->showMessage('The position has been reset.');
    
      if (!$this->_start_flag) {
        // Mark the game is started.
        $this->_start_flag = true;
        $this->showMessage('Game started!');
      }
      $this->report();
    }

    /*
     * set position for MOVE command
     */
    function move() {
      switch ($this->_current_position['f']) {
        case 'EAST' :
          if ($this->_current_position['x'] < self::TABLE_WIDTH - 1) $this->_current_position['x'] += 1;
          break;
        case 'SOUTH' :
          if ($this->_current_position['y'] > 0) $this->_current_position['y'] -= 1;
          break;
        case 'WEST' :
          if ($this->_current_position['x'] > 0) $this->_current_position['x'] -= 1;
          break;
        case 'NORTH' :
          if ($this->_current_position['y'] < self::TABLE_HEIGHT - 1) $this->_current_position['y'] += 1;
          break;
        case defualt:
          break;
      }
      return;
    }

    /*
     * set direction for LEFT and RIGHT command
     */
    function rotate($direction) {

      $_ind = array_search($this->_current_position['f'], self::DIRECTIONS);

      switch ($direction) {
        case 'LEFT':// anticlockwise
          $this->_current_position['f'] = $_ind == 0 ? self::DIRECTIONS[3] : self::DIRECTIONS[$_ind - 1];
          break;
        case 'RIGHT':// clockwise
          $this->_current_position['f'] = $_ind == 3 ? self::DIRECTIONS[0] : self::DIRECTIONS[$_ind + 1];
          break;
        default:
          break;
      }
      return;
    }

    /*
     * display the current position info
     */
    function report() {
      $this->showMessage($this->_current_position[x].','.$this->_current_position[y].','.$this->_current_position[f]);
      $this->displayTable();
    }

    /*
     * display the robot on the table
     */
    function displayTable () {
      for ($i = self::TABLE_HEIGHT - 1; $i >=0; $i--) {
        $row = '';
        for($j = 0; $j < self::TABLE_WIDTH; $j++) {
          if ($i === $this->_current_position['y'] && $j === $this->_current_position['x']) {
            $row .= self::ROBOT_ICONS[$this->_current_position['f']];
          } else {
            $row .= '*';
          }
        }
        $this->showMessage($row);
      }
    }

    /*
     * show message on the console
     */
    function showMessage($msg) {
      echo $msg.''.self::LINE_BREAK;
    } 
  }
?>