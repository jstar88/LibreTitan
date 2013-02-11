package models;

import java.util.*;
import play.db.ebean.*;
import play.data.validation.Constraints.*;
import javax.persistence.*;
import play.data.format.*;
import models.Login;
import interfaces.Loginable;
import com.avaje.ebean.*;
import javax.validation.*;

@Entity
@Table(name = "users")
public class User extends Model implements Loginable {
	
	public interface All {}
    public interface Step1{}
    public interface Step2{} 

	@Id
	public Long id;
	
	public Integer type;
	
	public String skin="blue";

	@Required(groups = {All.class, Step1.class})
    @Email(groups = {All.class, Step1.class})
	public String email;

	@Required(groups = {All.class, Step1.class})
    @MinLength(value = 1, groups = {All.class, Step1.class})
	public String name;

	@Required(groups = {All.class, Step1.class})
    @MinLength(value = 1, groups = {All.class, Step1.class})
	public String password;
	
	@Valid
	public Profile profile;

	@OneToOne(cascade = { CascadeType.ALL })
	public CelestialObject homePlanet;

	@OneToMany(cascade = { CascadeType.ALL })
	public List<CelestialObject> planets;

	@ManyToMany(cascade = { CascadeType.ALL })
	public List<Role> roles;

	public User(String name, String email, String password, Profile profile) {
		this(name, email, password);
		this.profile = profile;
	}

	public User(String name, String email, String password) {
		this.name = name;
		this.email = email;
		this.password = password;
	}

	public static class Profile {
		
		@Required(groups = {All.class, Step2.class})
		public String country;
		
		public String address;
		
		@Min(value = 1, groups = {All.class, Step2.class}) @Max(value = 100, groups = {All.class, Step2.class})
		public Integer age;

		public Profile()
		{
			
		}
		public Profile(String country, String address, Integer age) {
			this.country = country;
			this.address = address;
			this.age = age;
		}
	}

	/**
	 * Ad a role to current user.
	 */
	public void addRole(Integer id) throws ClassNotFoundException {
		Role role = Role.findById(id);
		if (role == null)
			throw new ClassNotFoundException("Role with id:" + id
					+ " not found.");
		roles.add(role);
	}

	/**
	 * Determinate if this user have the role with id as argument
	 */
	public boolean haveRole(Integer id) {
		Role tmp = new Role();
		tmp.id = id;
		return roles.contains(tmp);

	}

	/**
	 * Determinate if this user have the role as argument
	 */
	public boolean haveRole(Role role) {
		return roles.contains(role);

	}

	public String getPassword() {
		return password;
	}

	public String getName() {
		return name;
	}

	// ---------- override functions ------------
	public String toString() {
		return "User(" + email + ")";
	}

	public boolean equals(Object obj) {
		if (obj instanceof User) {
			User e = (User) obj;
			return e.id == id;
		} else
			return super.equals(obj);
	}

	// ---------- static functions ------------
	public static Model.Finder<String, User> find = new Model.Finder(
			String.class, User.class);

	/**
	 * Retrieve all users.
	 */
	public static List<User> findAll() {
		return find.all();
	}

	/**
	 * Retrieve a User from id.
	 */
	public static User findById(Long id) {
		return find.where().eq("id", id).findUnique();
	}

	/**
	 * Authenticate a User by name and password.
	 */
	public static User authenticate(Loginable login) {
		return find.where().eq("name", login.getName())
				.eq("password", login.getPassword()).findUnique();
	}

	public static boolean exist(User user) {
		Expression eqOrGt = Expr.or(Expr.eq("email", user.email), Expr.eq("name", user.name));
		return find.where().add(eqOrGt).findList() != null;
	}
	public static boolean existEmail(String email)
	{
		return find.where().eq("email", email).findUnique() != null;
	}
	public static boolean existName(String name)
	{
		return find.where().eq("name", name).findUnique() != null;
	}
}