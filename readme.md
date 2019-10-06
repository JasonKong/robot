Read me:
1 introduction
  1.1 The application is a simulation of a toy robot moving on a square tabletop, of dimensions 5 units x 5 units.
  1.2 Commands:
     a PLACE X,Y,F
       PLACE will put the toy robot on the table in position X,Y and facing NORTH, SOUTH, EAST or WEST.
       The origin (0,0) is at the SOUTH WEST most corner.
       The first valid command to the robot is a PLACE command, after that, any sequence of commands may be issued, in any order, including another PLACE command. 
       The application will discard all commands in the sequence until a valid PLACE command has been executed.
     b MOVE
       MOVE will move the toy robot one unit forward in the direction it is currently facing.
     c LEFT RIGHT
       LEFT and RIGHT will rotate the robot 90 degrees in the specified direction without changing the position of the robot.
     d REPORT
       REPORT will announce the X,Y and F of the robot.
       And show '↑', '→', '↓', '←' as the current robot on a 5*5 table, 
       for instace, the current position of 1,2,EAST will show like below
        *****
        *****
        *←***
        *****
        *****

2 Requirement to exucte
  PHP 5.0 or over

3 Install
  Copy all the files to your local

4 Run
  3.0 open the folder installed with any command line tools

  3.1 run with console
  php TestRobot.php

  3.2 run with input file
  php TestRobot.php -f test.txt 

  
