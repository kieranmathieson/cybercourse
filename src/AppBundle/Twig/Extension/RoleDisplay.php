<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Entity\User;
use AppBundle\Helper\Roles;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RoleDisplay extends \Twig_Extension
{

    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('show_roles_brief', [$this, 'showRolesBrief'])
        ];
    }

    /**
     * Return a short list of roles a user has.
     *
     * @param User $user The user.
     * @return string The list, for display.
     */
    public function showRolesBrief(User $user, $skipUserRole = true) {
        $roles = $user->getRoles();
        $shortLabels = [];
        foreach($roles as $role) {
            if ( ! array_key_exists($role, Roles::ROLE_LABELS) ) {
                throw new Exception('showRolesBrief: bad role: ' . $role);
            }
            if ( ! ($skipUserRole && $role === Roles::ROLE_USER) ) {
                $shortLabels[] = Roles::ROLE_LABELS[$role][Roles::ROLE_LABEL_SHORT];
            }
        }
        $result = implode(', ', $shortLabels);
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'role_display';
    }
}
