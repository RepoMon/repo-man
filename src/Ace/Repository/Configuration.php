<?php namespace Ace\Repository;

/*
 * @author tim rodger
 * Date: 29/03/15
 */
class Configuration
{
    public function getServiceName()
    {
        return 'Repository Monitor v4.0.0';
    }

    /**
     * @return string
     */
    public function getDbType()
    {
        getenv('DB_TYPE');
    }

    /**
     * @return string
     */
    public function getDbUser()
    {
        return 'root';
    }

    /**
     * @return string
     */
    public function getDbPassword()
    {
        $pw = getenv('MYSQL_ROOT_PASSWORD');

        if ($pw) {
            return $pw;
        } else {
            return '1234';
        }
    }

    public function getDbHost()
    {
        return 'mysql';
    }

    public function getDbName()
    {
        return 'repositories';
    }

    /**
     * @return string
     */
    public function getRabbitHost()
    {
        return getenv('RABBITMQ_PORT_5672_TCP_ADDR');
    }

    /**
     * @return string
     */
    public function getRabbitPort()
    {
        return getenv('RABBITMQ_PORT_5672_TCP_PORT');
    }

    /**
     * @return string
     */
    public function getRabbitChannelName()
    {
        // use an env var for the channel name too
        return 'repo-mon.main';
    }
}
