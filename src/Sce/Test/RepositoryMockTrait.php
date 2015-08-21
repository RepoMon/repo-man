<?php namespace Sce\Test; 

/**
 * @author timrodger
 * Date: 19/08/15
 */
trait RepositoryMockTrait
{
    protected $mock_store;

    protected $mock_repository;

    public function givenAMockStore()
    {
        $this->mock_store = $this->getMockBuilder('Sce\RepoMan\Store\StoreInterface')
            ->getMock();

        $this->mock_store->expects($this->any())
            ->method('getAll')
            ->will($this->returnValue($this->repositories));
    }

    public function givenAMockRepository($url, $config_json, $lock_json, $latest_tag, $checked_out = true)
    {
        $mock_repository = $this->getMockBuilder('Sce\RepoMan\Domain\Repository')
            ->disableOriginalConstructor()
            ->getMock();

        $mock_repository->expects($this->any())
            ->method('isCheckedout')
            ->will($this->returnValue($checked_out));

        $mock_repository->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue($url));

        $mock_repository->expects($this->at(1))
            ->method('getFile')
            ->will($this->returnValue($config_json));

        $mock_repository->expects($this->at(2))
            ->method('getFile')
            ->will($this->returnValue($lock_json));

        $mock_repository->expects($this->any())
            ->method('getLatestTag')
            ->will($this->returnValue($latest_tag));

        $this->repositories []= $mock_repository;
    }

    public function givenADependency($repo, $dependency, $version, $lock)
    {

        list($repo_uri, $latest_tag) = explode('|', $repo);
        $config_data = ['require' => [$dependency => $version]];

        $time = "2015-07-10 06:54:46";
        $lock_data = ["packages" => [
            ['name' => $dependency, 'version' => $lock, 'time' => $time]
        ]];

        $this->givenAMockRepository($repo_uri, json_encode($config_data), json_encode($lock_data), $latest_tag);
    }

}