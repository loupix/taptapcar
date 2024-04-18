<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;
use UserBundle\Entity\Vehicule;

/**
 * Profile
 *
 * @ORM\Table(name="user_profile")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\ProfileRepository")
 */
class Profile
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var user
     *
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\User", inversedBy="profile")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;


    /**
     * @var pays
     *
     * @ORM\OneToOne(targetEntity="GeographieBundle\Entity\Pays", cascade={"persist"})
     * @ORM\JoinColumn(name="pays_id", referencedColumnName="pays_id")
     */
    private $pays;


    /**
     * @var region
     *
     * @ORM\OneToOne(targetEntity="GeographieBundle\Entity\Region", cascade={"persist"})
     * @ORM\JoinColumn(name="region_id", referencedColumnName="region_id")
     */
    private $region;


    /**
     * @var ville
     *
     * @ORM\OneToOne(targetEntity="GeographieBundle\Entity\Ville", cascade={"persist"})
     * @ORM\JoinColumn(name="ville_id", referencedColumnName="ville_id")
     */
    private $ville;

    

    /**
     * @var vehicule
     *
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\Vehicule", mappedBy="profile")
     * @ORM\JoinColumn(name="vehicule_id", referencedColumnName="id")
     */
    private $vehicule;


    /**
     * @var string
     *
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\Photo", mappedBy="profile")
     * @ORM\JoinColumn(name="photo_id", referencedColumnName="id")
     */
    private $photo;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=255, nullable=true)
     */
    private $telephone;


    /**
     * @var string
     *
     * @ORM\Column(name="userPaie_id", type="string", nullable=true)
     */
    private $userPaie;


    /**
     * @var string
     *
     * @ORM\Column(name="userLegal_id", type="string", nullable=true)
     */
    private $userLegal;


    /**
     * @var string
     *
     * @ORM\Column(name="walletPaie_id", type="string", nullable=true)
     */
    private $walletPaie;

    /**
     * @var string
     *
     * @ORM\Column(name="bankAccount_id", type="string", nullable=true)
     */
    private $bankAccount;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return Profile
     */
    public function setUser(\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * Set vehicule
     *
     * @param integer $vehicule
     *
     * @return Profile
     */
    public function setVehicule($vehicule)
    {
        $this->vehicule = $vehicule;

        return $this;
    }

    /**
     * Get vehicule
     *
     * @return int
     */
    public function getVehicule()
    {
        return $this->vehicule;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Profile
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }



    

    /**
     * Set pays
     *
     * @param \GegraphieBundle\Entity\Pays $pays
     *
     * @return Profile
     */
    public function setPays($pays = null)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return \GegraphieBundle\Entity\Pays
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set region
     *
     * @param \GegraphieBundle\Entity\Region $region
     *
     * @return Profile
     */
    public function setRegion($region = null)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return \GegraphieBundle\Entity\Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set ville
     *
     * @param \GeographieBundle\Entity\Ville $ville
     *
     * @return Profile
     */
    public function setVille($ville = null)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return \GeographieBundle\Entity\Ville
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set photo
     *
     * @param \UserBundle\Entity\Photo $photo
     *
     * @return Profile
     */
    public function setPhoto($photo = null)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return \UserBundle\Entity\Photo
     */
    public function getPhoto()
    {
        if(!is_null($this->photo)){
            return $this->photo;
        }else{
            $p = new Photo();
            $p->setWidth(120);
            $p->setHeight(120);
            $p->setSrc("/bundles/application/images/profil.jpg");
            return $p;
        }
        
    }


    /**
     * Set userPaie
     *
     * @param string $userPaie
     *
     * @return Profile
     */
    public function setUserPaie($userPaie)
    {
        $this->userPaie = $userPaie;

        return $this;
    }

    /**
     * Get userPaie
     *
     * @return string
     */
    public function getUserPaie()
    {
        return $this->userPaie;
    }


    /**
     * Set walletPaie
     *
     * @param string $walletPaie
     *
     * @return Profile
     */
    public function setWalletPaie($walletPaie)
    {
        $this->walletPaie = $walletPaie;

        return $this;
    }

    /**
     * Get walletPaie
     *
     * @return string
     */
    public function getWalletPaie()
    {
        return $this->walletPaie;
    }

    /**
     * Set bankAccount
     *
     * @param string $bankAccount
     *
     * @return Profile
     */
    public function setBankAccount($bankAccount)
    {
        $this->bankAccount = $bankAccount;

        return $this;
    }

    /**
     * Get bankAccount
     *
     * @return string
     */
    public function getBankAccount()
    {
        return $this->bankAccount;
    }


    /**
     * Set userLegal
     *
     * @param string $userLegal
     *
     * @return Profile
     */
    public function setUserLegal($userLegal)
    {
        $this->userLegal = $userLegal;

        return $this;
    }

    /**
     * Get userLegal
     *
     * @return string
     */
    public function getUserLegal()
    {
        return $this->userLegal;
    }



    public function toArray(){
        return array(
            'Id'=>$this->getId(),
            'Telephone'=>$this->getTelephone(),
            'Photo'=>$this->getPhoto()->toArray(),
            'Vehicule'=>is_null($this->getVehicule()) ? null : $this->getVehicule()->toArray(),
            'Ville'=>is_null($this->getVille()) ? null : $this->getVille()->toArray()
        );
    }

    

    
}
