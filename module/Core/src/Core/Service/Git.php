<?php
namespace Core\Service;

class Git extends AbstractService
{
    use \Core\Traits\ServiceOption;

    protected $repo_path = null;
    protected $bare = false;
    protected $envopts = array();
    public static $dataConf;

    protected static $bin;

    public static function set_bin($path) {
        self::$bin = $path;
    }

    public static function get_bin() {
        return self::$bin;
    }

    public static function windows_mode() {
        self::set_bin('git');
    }

    public static function &create_new($repo_path, $source = null, $remote_source = false, $reference = null) {
        if (is_dir($repo_path) && file_exists($repo_path."/.git") && is_dir($repo_path."/.git")) {
            throw new \Exception('"'.$repo_path.'" is already a git repository');
        } else {
            $repo = new self($repo_path, true, false);
            if (is_string($source)) {
                if ($remote_source) {
                    if (!is_dir($reference) || !is_dir($reference.'/.git')) {
                        throw new \Exception('"'.$reference.'" is not a git repository. Cannot use as reference.');
                    } else if (strlen($reference)) {
                        $reference = realpath($reference);
                        $reference = "--reference $reference";
                    }
                    $repo->clone_remote($source, $reference);
                } else {
                    $repo->clone_from($source);
                }
            } else {
                $repo->run('init');
            }
            return $repo;
        }
    }

    public function __construct($serviceLocator) {
        $this->setServiceLocator($serviceLocator);
        if(!self::$bin){
            self::$bin = $this->getServiceOption()->get('core', 'github_executable_path')->getValue();
        }
        $repo_path = dirname(dirname(dirname(PUBLIC_PATH)));
        if (is_string($repo_path)) {
            $this->set_repo_path($repo_path, false, true);
        }
    }

    public function updateAuthFile()
    {
        if (is_file($this->repo_path . "/.git/config")) {
            $data = file_get_contents($this->repo_path . "/.git/config");

            $username = $this->getServiceOption()->get('core', 'github_username')->getValue();
            $password = $this->getServiceOption()->get('core', 'github_password')->getValue();

            $data = preg_replace('/https(.*)@/', "https://$username:$password@", $data);

            file_put_contents($this->repo_path . "/.git/config", $data);
        }
    }

    public function set_repo_path($repo_path, $create_new = false, $_init = true) {

        if (is_string($repo_path)) {

            if ($new_path = realpath($repo_path)) {
                $repo_path = $new_path;
                if (is_dir($repo_path)) {
                    // Is this a work tree?

                    if (file_exists($repo_path."/.git") && is_dir($repo_path."/.git")) {
                        $this->repo_path = $repo_path;
                        $this->bare = false;
                        // Is this a bare repo?
                    } else if (is_file($repo_path."/config")) {
                        $parse_ini = parse_ini_file($repo_path."/config");
                        if ($parse_ini['bare']) {
                            $this->repo_path = $repo_path;
                            $this->bare = true;
                        }
                    } else {
                        if ($create_new) {
                            $this->repo_path = $repo_path;
                            if ($_init) {
                                $this->run('init');
                            }
                        } else {
                            throw new \Exception('"'.$repo_path.'" is not a git repository');
                        }
                    }
                } else {
                    throw new \Exception('"'.$repo_path.'" is not a directory');
                }
            } else {
                if ($create_new) {
                    if ($parent = realpath(dirname($repo_path))) {
                        mkdir($repo_path);
                        $this->repo_path = $repo_path;
                        if ($_init) $this->run('init');
                    } else {
                        throw new \Exception('cannot create repository in non-existent directory');
                    }
                } else {
                    throw new \Exception('"'.$repo_path.'" does not exist');
                }
            }
        }
    }

    public function git_directory_path() {
        return ($this->bare) ? $this->repo_path : $this->repo_path."/.git";
    }

    public function test_git() {
        $descriptorspec = array(
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'),
        );
        $pipes = array();
        $resource = proc_open(Git::get_bin(), $descriptorspec, $pipes);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        foreach ($pipes as $pipe) {
            fclose($pipe);
        }
        $status = trim(proc_close($resource));
        return ($status != 127);
    }

    protected function run_command($command) {
        $descriptorspec = array(
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'),
        );
        $pipes = array();

        if(count($_ENV) === 0) {
            $env = NULL;
            foreach($this->envopts as $k => $v) {
                putenv(sprintf("%s=%s",$k,$v));
            }
        } else {
            $env = array_merge($_ENV, $this->envopts);
        }
        $cwd = $this->repo_path;

        if(substr_count($command, ' pull ')){
            $command = 'sudo php ' . $this->repo_path . '/command.php "sleep 2 && cd ' . $this->repo_path . ' && ' . $command . '"';
        }
        $resource = proc_open($command, $descriptorspec, $pipes, $cwd, $env);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        foreach ($pipes as $pipe) {
            fclose($pipe);
        }
        $status = trim(proc_close($resource));
        if ($status) throw new \Exception($stderr);



        return $stdout;
    }

    public function run($command) {
        return $this->run_command(Git::get_bin()." ".$command);
    }

    public function status($html = false) {
        $msg = $this->run("status");
        if ($html == true) {
            $msg = str_replace("\n", "<br />", $msg);
        }
        return $msg;
    }

    public function add($files = "*") {
        if (is_array($files)) {
            $files = '"'.implode('" "', $files).'"';
        }
        return $this->run("add $files -v");
    }

    public function rm($files = "*", $cached = false) {
        if (is_array($files)) {
            $files = '"'.implode('" "', $files).'"';
        }
        return $this->run("rm ".($cached ? '--cached ' : '').$files);
    }

    public function commit($message = "", $commit_all = true) {
        $flags = $commit_all ? '-av' : '-v';
        return $this->run("commit ".$flags." -m ".escapeshellarg($message));
    }

    public function clone_to($target) {
        return $this->run("clone --local ".$this->repo_path." $target");
    }

    public function clone_from($source) {
        return $this->run("clone --local $source ".$this->repo_path);
    }

    public function clone_remote($source, $reference) {
        return $this->run("clone $reference $source ".$this->repo_path);
    }

    public function clean($dirs = false, $force = false) {
        return $this->run("clean".(($force) ? " -f" : "").(($dirs) ? " -d" : ""));
    }

    public function create_branch($branch) {
        return $this->run("branch $branch");
    }

    public function delete_branch($branch, $force = false) {
        return $this->run("branch ".(($force) ? '-D' : '-d')." $branch");
    }

    public function list_branches($keep_asterisk = false) {
        $branchArray = explode("\n", $this->run("branch"));
        foreach($branchArray as $i => &$branch) {
            $branch = trim($branch);
            if (! $keep_asterisk) {
                $branch = str_replace("* ", "", $branch);
            }
            if ($branch == "") {
                unset($branchArray[$i]);
            }
        }
        return $branchArray;
    }

    public function list_remote_branches() {
        $branchArray = explode("\n", $this->run("branch -r"));
        foreach($branchArray as $i => &$branch) {
            $branch = trim($branch);
            if ($branch == "" || strpos($branch, 'HEAD -> ') !== false) {
                unset($branchArray[$i]);
            }
        }
        return $branchArray;
    }

    public function active_branch($keep_asterisk = false) {
        $branchArray = $this->list_branches(true);
        $active_branch = preg_grep("/^\*/", $branchArray);
        reset($active_branch);
        if ($keep_asterisk) {
            return current($active_branch);
        } else {
            return str_replace("* ", "", current($active_branch));
        }
    }

    public function checkout($branch) {
        return $this->run("checkout $branch");
    }

    public function merge($branch) {
        return $this->run("merge $branch --no-ff");
    }

    public function fetch() {
        return $this->run("fetch");
    }

    public function add_tag($tag, $message = null) {
        if ($message === null) {
            $message = $tag;
        }
        return $this->run("tag -a $tag -m " . escapeshellarg($message));
    }

    public function list_tags($pattern = null) {
        $tagArray = explode("\n", $this->run("tag -l $pattern"));
        foreach ($tagArray as $i => &$tag) {
            $tag = trim($tag);
            if ($tag == '') {
                unset($tagArray[$i]);
            }
        }
        return $tagArray;
    }

    public function push($remote, $branch) {
        return $this->run("push --tags $remote $branch");
    }

    public function pull() {
        $remote = $this->getServiceOption()->get('core', 'github_remote')->getValue();
        $branch = $this->getServiceOption()->get('core', 'github_branch')->getValue();
        return $this->run("pull $remote $branch");
    }

    public function log($format = null) {
        if ($format === null)
            return $this->run('log');
        else
            return $this->run('log --pretty=format:"' . $format . '"');
    }

    public function set_description($new) {
        $path = $this->git_directory_path();
        file_put_contents($path."/description", $new);
    }

    public function get_description() {
        $path = $this->git_directory_path();
        return file_get_contents($path."/description");
    }

    public function setenv($key, $value) {
        $this->envopts[$key] = $value;
    }
}