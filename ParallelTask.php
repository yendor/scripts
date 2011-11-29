<?php
abstract class ParallelTask
{
	/**
     * The maximum number of child processed to spawn to do work
     * @var integer
     */
	public $max_workers = 8;

	/**
     * The main execution loop. This should not have to change in sub classes
     *
     * @return void
     * @author Rodney Amato
     */
	public function run()
	{
		$num_workers = 0;
		$items = $this->get_data();
		foreach ($items as $item) {
			while ($num_workers < $this->max_workers) {
				$pid = pcntl_fork();

				// Error
				if ($pid == -1) {
					die('could not fork');
				}
				// Worker
				elseif ($pid === 0) {
					if ($this->do_work($item)) {
						exit(0);
					}
					else {
						exit(1);
					}
				}
				// Parent
				else {
					$num_workers++;
				}
			}
			// Wait until a child exits before trying to do more work
			pcntl_wait($status); //Protect against Zombie children
			$num_workers--;
		}
	}

    /**
     * Do some sort of work with a single item from the array returned by get_data.
     * This function should always establish any resources (like db connections) from scratch as well
     * as dieing or exiting at the completion of it's work for the single item
     *
	 * @return void
     * @author Rodney Amato
     */
	abstract protected function do_work($item);

	/**
	 * Fetch the data from whatever datasource you like and returns it as an array that can be later iterated over
	 *
	 * @return array
	 * @author Rodney Amato
	 **/
	abstract protected function get_data();
}

