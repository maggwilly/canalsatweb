<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * JourneeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class JourneeRepository extends EntityRepository
{
	public function findToday($user)
  {
    $dateObject = new \DateTime('now');
    $dateObject->setTime(0, 0, 0); // Modify to 2013-06-10 00:00:00, beginning of the day   
    $yesteDay=$dateObject->modify('-1 day'); // Have 2013-06-11 00:00:00   
  $qb = $this->createQueryBuilder('m')
  ->where('m.date >:yesteDay')
  ->andWhere('m.user=:user')
  ->setParameter('yesteDay',$yesteDay)
  ->setParameter('user',$user); 
       $qb->orderBy('m.date', 'DESC');
  return $qb
     ->getQuery()
     ->getOneOrNullResult();
  ;

}

public function findByUser($user=null,$date=null)
{
  $qb = $this->createQueryBuilder('c');
  if($date!=null){
      $qb->where('c.date=:date')
       ->setParameter('date', new \DateTime($date));
        }    
	 if($user!=null){
		$qb->andWhere('c.user=:user')->setParameter('user', $user);
	  }  
     
     $qb->orderBy('c.date', 'DESC');
  return $qb
     ->getQuery()
    ->getArrayResult();
  ;

}
}
